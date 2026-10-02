/**
 * Reusable Browser-Native Web Speech API Text-to-Speech (TTS) Service
 * Hindi PDF Library (hindipdflibrary.com)
 */

class TextToSpeechService {
    constructor() {
        this.synth = window.speechSynthesis || null;
        this.utterance = null;
        this.chunks = [];
        this.currentChunkIndex = 0;
        this.isPlaying = false;
        this.isPaused = false;
        this.rate = 1.0;
        this.pitch = 1.0;
        this.onStateChangeCallback = null;
        this.activeVoices = [];

        if (this.synth) {
            this.loadVoices();
            if (speechSynthesis.onvoiceschanged !== undefined) {
                speechSynthesis.onvoiceschanged = () => this.loadVoices();
            }

            // Teardown safety: immediately cancel audio on page navigation
            window.addEventListener('beforeunload', () => this.stop());
            window.addEventListener('pagehide', () => this.stop());
            window.addEventListener('popstate', () => this.stop());
            document.addEventListener('livewire:navigating', () => this.stop());
        }
    }

    loadVoices() {
        if (!this.synth) return;
        this.activeVoices = this.synth.getVoices();
    }

    isSupported() {
        return !!this.synth;
    }

    /**
     * Detect language automatically based on presence of Devanagari Unicode characters.
     */
    detectLanguage(text) {
        const hasDevanagari = /[\u0900-\u097F]/.test(text);
        return hasDevanagari ? 'hi-IN' : 'en-IN';
    }

    /**
     * Select the best voice for the given language locale.
     */
    getVoiceForLanguage(locale) {
        if (!this.activeVoices || this.activeVoices.length === 0) {
            this.loadVoices();
        }

        const isHindi = locale.startsWith('hi');

        // Look for exact locale match first
        let voice = this.activeVoices.find(v => v.lang.toLowerCase() === locale.toLowerCase());

        // Look for language prefix match
        if (!voice && isHindi) {
            voice = this.activeVoices.find(v => v.lang.toLowerCase().startsWith('hi'));
        }

        if (!voice && !isHindi) {
            voice = this.activeVoices.find(v => v.lang.toLowerCase().includes('en-in')) ||
                    this.activeVoices.find(v => v.lang.toLowerCase().startsWith('en'));
        }

        return voice || null;
    }

    /**
     * Split long text into safe, punctuation-aware chunks (under 200 chars) to prevent Chrome/Edge cutoff.
     */
    chunkText(text) {
        if (!text) return [];

        // Normalize text
        const clean = text.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
        
        // Regex for split boundaries: Hindi danda (।), double danda (॥), period, exclamation, question, newline
        const sentenceRegex = /([^।॥.!?\n]+[।॥.!?\n]+)/g;
        let matches = clean.match(sentenceRegex);

        if (!matches || matches.length === 0) {
            matches = [clean];
        }

        const safeChunks = [];
        for (let chunk of matches) {
            chunk = chunk.trim();
            if (!chunk) continue;

            if (chunk.length <= 180) {
                safeChunks.push(chunk);
            } else {
                // Further split by commas or words if a sentence is too long
                const words = chunk.split(' ');
                let current = '';
                for (const word of words) {
                    if ((current + ' ' + word).trim().length > 180) {
                        if (current) safeChunks.push(current.trim());
                        current = word;
                    } else {
                        current = (current + ' ' + word).trim();
                    }
                }
                if (current) safeChunks.push(current.trim());
            }
        }

        return safeChunks;
    }

    /**
     * Speak text using SpeechSynthesis.
     */
    play(text, lang = null) {
        if (!this.synth) {
            console.warn('SpeechSynthesis is not supported in this browser.');
            return;
        }

        this.stop();

        const targetLang = lang || this.detectLanguage(text);
        this.chunks = this.chunkText(text);
        if (this.chunks.length === 0) return;

        this.currentChunkIndex = 0;
        this.isPlaying = true;
        this.isPaused = false;
        this.notifyState('playing');

        this.speakNextChunk(targetLang);
    }

    speakNextChunk(lang) {
        if (!this.isPlaying || this.currentChunkIndex >= this.chunks.length) {
            this.stop();
            return;
        }

        const chunk = this.chunks[this.currentChunkIndex];
        const utterance = new SpeechSynthesisUtterance(chunk);
        utterance.lang = lang;
        utterance.rate = this.rate;
        utterance.pitch = this.pitch;

        const voice = this.getVoiceForLanguage(lang);
        if (voice) {
            utterance.voice = voice;
        }

        utterance.onend = () => {
            if (this.isPlaying && !this.isPaused) {
                this.currentChunkIndex++;
                this.speakNextChunk(lang);
            }
        };

        utterance.onerror = (e) => {
            console.error('Speech synthesis chunk error:', e);
            if (this.isPlaying && !this.isPaused) {
                this.currentChunkIndex++;
                this.speakNextChunk(lang);
            }
        };

        this.utterance = utterance;
        this.synth.speak(utterance);
    }

    pause() {
        if (this.synth && this.isPlaying && !this.isPaused) {
            this.synth.pause();
            this.isPaused = true;
            this.notifyState('paused');
        }
    }

    resume() {
        if (this.synth && this.isPlaying && this.isPaused) {
            this.synth.resume();
            this.isPaused = false;
            this.notifyState('playing');
        }
    }

    stop() {
        if (this.synth) {
            this.synth.cancel();
        }
        this.isPlaying = false;
        this.isPaused = false;
        this.chunks = [];
        this.currentChunkIndex = 0;
        this.notifyState('stopped');
    }

    setRate(newRate) {
        this.rate = parseFloat(newRate);
        if (this.isPlaying && !this.isPaused && this.chunks.length > 0) {
            // Restart current chunk with new rate
            const currentLang = this.detectLanguage(this.chunks[this.currentChunkIndex] || '');
            this.synth.cancel();
            this.speakNextChunk(currentLang);
        }
    }

    onStateChange(callback) {
        this.onStateChangeCallback = callback;
    }

    notifyState(state) {
        if (typeof this.onStateChangeCallback === 'function') {
            this.onStateChangeCallback({
                state,
                isPlaying: this.isPlaying,
                isPaused: this.isPaused,
                currentChunk: this.currentChunkIndex + 1,
                totalChunks: this.chunks.length,
            });
        }
        window.dispatchEvent(new CustomEvent('tts:state', {
            detail: {
                state,
                isPlaying: this.isPlaying,
                isPaused: this.isPaused,
            }
        }));
    }
}

// Global instance
window.textToSpeech = new TextToSpeechService();
export default window.textToSpeech;
