<div 
    id="global-audio-player"
    x-data="{
        isOpen: false,
        isPlaying: false,
        currentTitle: '',
        currentAuthor: '',
        currentText: '',
        rate: 1.0,
        utterance: null,
        progress: 0,
        availableVoices: [],

        init() {
            window.addEventListener('play-book-audio', (e) => {
                this.playAudio(e.detail.title, e.detail.author, e.detail.text, e.detail.lang);
            });

            if ('speechSynthesis' in window) {
                const loadVoices = () => {
                    this.availableVoices = window.speechSynthesis.getVoices();
                };
                loadVoices();
                window.speechSynthesis.onvoiceschanged = loadVoices;
            }
        },

        findVoice(isHindi) {
            const voices = this.availableVoices && this.availableVoices.length > 0 
                ? this.availableVoices 
                : window.speechSynthesis.getVoices();

            if (!voices || voices.length === 0) return null;

            if (isHindi) {
                // Priority 1: High quality natural / online Hindi voice
                let v = voices.find(voice => 
                    (voice.lang === 'hi-IN' || voice.lang === 'hi_IN' || voice.lang.toLowerCase().startsWith('hi')) &&
                    (voice.name.includes('Natural') || voice.name.includes('Online') || voice.name.includes('Google'))
                );
                if (v) return v;

                // Priority 2: Any voice with hi-IN or hi* language tag
                v = voices.find(voice => 
                    voice.lang === 'hi-IN' || voice.lang === 'hi_IN' || voice.lang.toLowerCase().startsWith('hi')
                );
                if (v) return v;

                // Priority 3: Voice with Hindi / हिन्दी / Swara / Madhur / Hemant / Kalpana in name
                v = voices.find(voice => {
                    const n = (voice.name || '').toLowerCase();
                    return n.includes('hindi') || n.includes('हिन्दी') || n.includes('swara') || n.includes('madhur') || n.includes('hemant') || n.includes('kalpana');
                });
                if (v) return v;

                // CRITICAL: Return null so browser's native hi-IN engine is used instead of forcing an English voice
                return null;
            } else {
                return voices.find(v => v.lang.startsWith('en') && (v.name.includes('Natural') || v.name.includes('Online') || v.name.includes('Google')))
                    || voices.find(v => v.lang.startsWith('en'))
                    || null;
            }
        },

        playAudio(title, author, text, preferredLang = null) {
            this.currentTitle = title || 'Hindi PDF Book';
            this.currentAuthor = author || '';
            this.currentText = text || (title + (author ? ' by ' + author : '') + '. यह पुस्तक हिन्दी पीडीएफ पुस्तकालय पर पढ़ने और सुनने के लिए उपलब्ध है।');
            this.isOpen = true;

            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();

                // Small delay to allow Chrome/Edge speech engine to cancel previous utterances cleanly
                setTimeout(() => {
                    const cleanText = this.currentText.replace(/[*#_`~[\]()]/g, ' ').replace(/\s+/g, ' ').trim();
                    this.utterance = new SpeechSynthesisUtterance(cleanText);
                    this.utterance.rate = this.rate || 1.0;

                    const isHindi = preferredLang === 'hi' || /[\u0900-\u097F]/.test(cleanText);
                    this.utterance.lang = isHindi ? 'hi-IN' : 'en-US';

                    const matchedVoice = this.findVoice(isHindi);
                    if (matchedVoice) {
                        this.utterance.voice = matchedVoice;
                    }

                    this.utterance.onstart = () => {
                        this.isPlaying = true;
                    };

                    this.utterance.onend = () => {
                        this.isPlaying = false;
                    };

                    this.utterance.onerror = (e) => {
                        console.warn('TTS error', e);
                        if (e.error !== 'interrupted') {
                            this.isPlaying = false;
                        }
                    };

                    window.speechSynthesis.speak(this.utterance);
                    this.isPlaying = true;
                }, 50);
            } else {
                alert('Audio narration is not supported on your current browser. Please try Chrome, Safari, or Edge.');
            }
        },

        togglePlay() {
            if (!('speechSynthesis' in window)) return;

            if (this.isPlaying) {
                window.speechSynthesis.pause();
                this.isPlaying = false;
            } else {
                if (window.speechSynthesis.paused) {
                    window.speechSynthesis.resume();
                    this.isPlaying = true;
                } else if (this.currentText) {
                    this.playAudio(this.currentTitle, this.currentAuthor, this.currentText);
                }
            }
        },

        setRate(newRate) {
            this.rate = newRate;
            if (this.isPlaying) {
                this.playAudio(this.currentTitle, this.currentAuthor, this.currentText);
            }
        },

        closePlayer() {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
            }
            this.isPlaying = false;
            this.isOpen = false;
        }
    }"
    x-show="isOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed bottom-4 right-4 left-4 sm:left-auto sm:w-96 z-50 rounded-2xl bg-slate-900 text-white p-4 shadow-2xl border border-slate-800 backdrop-blur-md"
    style="display: none;"
>
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <!-- Animated Waveform / Disc -->
            <div class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#48b3e8] text-white shadow-xs">
                <template x-if="isPlaying">
                    <div class="flex items-end gap-0.5 h-4">
                        <span class="w-1 bg-white rounded-full animate-pulse h-3"></span>
                        <span class="w-1 bg-white rounded-full animate-pulse h-4" style="animation-delay: 150ms"></span>
                        <span class="w-1 bg-white rounded-full animate-pulse h-2" style="animation-delay: 300ms"></span>
                    </div>
                </template>
                <template x-if="!isPlaying">
                    <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z" />
                    </svg>
                </template>
            </div>

            <!-- Title & Author -->
            <div class="min-w-0 flex-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-sky-400 block">
                    Listening to Book • ऑडियो वाचन
                </span>
                <h4 class="text-xs font-bold truncate text-white" x-text="currentTitle"></h4>
                <p class="text-[11px] text-slate-400 truncate" x-text="currentAuthor"></p>
            </div>
        </div>

        <!-- Close button -->
        <button 
            type="button" 
            @click="closePlayer()" 
            class="text-slate-400 hover:text-white p-1 rounded-lg transition"
            aria-label="Close audio player"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Controls Row -->
    <div class="mt-3 pt-3 border-t border-slate-800 flex items-center justify-between">
        <!-- Play / Pause -->
        <div class="flex items-center gap-2">
            <button 
                type="button" 
                @click="togglePlay()" 
                class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-900 hover:bg-slate-200 transition"
            >
                <svg x-show="!isPlaying" class="h-4 w-4 fill-current ml-0.5" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z" />
                </svg>
                <svg x-show="isPlaying" class="h-4 w-4 fill-current" viewBox="0 0 24 24" style="display: none;">
                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                </svg>
            </button>
            <span class="text-[11px] font-medium text-slate-300" x-text="isPlaying ? 'Playing...' : 'Paused'"></span>
        </div>

        <!-- Speed toggles -->
        <div class="flex items-center gap-1 bg-slate-800 p-0.5 rounded-lg text-[10px] font-bold">
            <button 
                type="button" 
                @click="setRate(0.9)" 
                class="px-2 py-0.5 rounded transition"
                :class="rate === 0.9 ? 'bg-[#48b3e8] text-white' : 'text-slate-400 hover:text-white'"
            >
                0.9x
            </button>
            <button 
                type="button" 
                @click="setRate(1.0)" 
                class="px-2 py-0.5 rounded transition"
                :class="rate === 1.0 ? 'bg-[#48b3e8] text-white' : 'text-slate-400 hover:text-white'"
            >
                1.0x
            </button>
            <button 
                type="button" 
                @click="setRate(1.2)" 
                class="px-2 py-0.5 rounded transition"
                :class="rate === 1.2 ? 'bg-[#48b3e8] text-white' : 'text-slate-400 hover:text-white'"
            >
                1.2x
            </button>
        </div>
    </div>
</div>
