@props([
    'targetTextId' => null,
    'rawText' => '',
    'label' => 'सुनें (Listen)',
    'class' => '',
])

<div x-data="{
    isPlaying: false,
    isPaused: false,
    isLoading: false,
    rate: 1.0,
    progress: '',
    chunks: [],
    chunkIndex: 0,
    audio: null,
    timer: null,

    init() {
        this.audio = new Audio();

        this.audio.addEventListener('playing', () => {
            this.isLoading = false;
        });

        this.audio.addEventListener('waiting', () => {
            this.isLoading = true;
        });

        this.audio.addEventListener('ended', () => {
            this.onChunkEnded();
        });

        this.audio.addEventListener('error', (e) => {
            console.warn('TTS stream error, skipping to next chunk:', e);
            this.isLoading = false;
            if (this.isPlaying && !this.isPaused) {
                this.timer = setTimeout(() => {
                    this.chunkIndex++;
                    if (this.chunkIndex < this.chunks.length) {
                        this.playCurrentChunk();
                    } else {
                        this.stop();
                    }
                }, 500);
            }
        });

        // Teardown safety: cancel audio on page navigation or unload
        window.addEventListener('beforeunload', () => this.stop());
        window.addEventListener('pagehide', () => this.stop());
        window.addEventListener('popstate', () => this.stop());
        document.addEventListener('livewire:navigating', () => this.stop());
    },

    getText() {
        @if($targetTextId)
            const el = document.getElementById('{{ $targetTextId }}');
            return el ? (el.innerText || el.textContent || '') : '';
        @else
            return {!! json_encode($rawText) !!};
        @endif
    },

    chunkText(raw) {
        if (!raw) return [];

        const clean = raw
            .replace(/<br\s*[\/]?>/gi, '\n')
            .replace(/<\/p>/gi, '\n\n')
            .replace(/<\/div>/gi, '\n')
            .replace(/<[^>]*>/g, ' ')
            .replace(/&nbsp;/g, ' ')
            .replace(/\r\n/g, '\n')
            .replace(/\r/g, '\n');

        const lines = clean.split('\n');
        const chunks = [];

        for (let line of lines) {
            line = line.trim();
            if (!line) continue;

            const parts = line.split(/(?<=[।॥.!?])/g);
            for (let part of parts) {
                part = part.trim();
                if (!part) continue;

                if (part.length <= 180) {
                    chunks.push(part);
                } else {
                    const subparts = part.split(/(?<=[,;:])/g);
                    for (let sub of subparts) {
                        sub = sub.trim();
                        if (!sub) continue;

                        if (sub.length <= 180) {
                            chunks.push(sub);
                        } else {
                            const words = sub.split(/\s+/);
                            let current = '';
                            for (const word of words) {
                                if ((current + ' ' + word).trim().length > 150) {
                                    if (current) chunks.push(current.trim());
                                    current = word;
                                } else {
                                    current = (current + ' ' + word).trim();
                                }
                            }
                            if (current) chunks.push(current.trim());
                        }
                    }
                }
            }
        }
        return chunks;
    },

    play() {
        if (this.isPaused) {
            this.resume();
            return;
        }

        const fullText = this.getText();
        if (!fullText || !fullText.trim()) {
            console.warn('TTS: No text content found to read.');
            return;
        }

        this.stop();

        this.chunks = this.chunkText(fullText);
        if (this.chunks.length === 0) return;

        this.chunkIndex = 0;
        this.isPlaying = true;
        this.isPaused = false;
        this.isLoading = true;
        this.updateProgress();

        this.playCurrentChunk();
    },

    playCurrentChunk() {
        if (!this.isPlaying || this.isPaused || this.chunkIndex >= this.chunks.length) {
            if (this.chunkIndex >= this.chunks.length) {
                this.stop();
            }
            return;
        }

        const chunk = this.chunks[this.chunkIndex];
        if (!chunk || !chunk.trim()) {
            this.chunkIndex++;
            this.playCurrentChunk();
            return;
        }

        this.updateProgress();
        this.isLoading = true;

        const url = '{{ route('tts.stream') }}?text=' + encodeURIComponent(chunk);
        this.audio.src = url;
        this.audio.playbackRate = this.rate;

        const playPromise = this.audio.play();
        if (playPromise !== undefined) {
            playPromise.then(() => {
                this.isLoading = false;
                this.preloadNextChunk();
            }).catch((err) => {
                console.warn('Audio play request interrupted or delayed:', err);
                this.isLoading = false;
            });
        }
    },

    preloadNextChunk() {
        if (this.chunkIndex + 1 < this.chunks.length) {
            const nextText = this.chunks[this.chunkIndex + 1];
            if (nextText && nextText.trim()) {
                const nextUrl = '{{ route('tts.stream') }}?text=' + encodeURIComponent(nextText);
                fetch(nextUrl, { priority: 'low' }).catch(() => {});
            }
        }
    },

    onChunkEnded() {
        if (!this.isPlaying || this.isPaused) return;

        this.chunkIndex++;
        if (this.chunkIndex < this.chunks.length) {
            this.updateProgress();
            this.timer = setTimeout(() => {
                if (this.isPlaying && !this.isPaused) {
                    this.playCurrentChunk();
                }
            }, 350);
        } else {
            this.stop();
        }
    },

    pause() {
        if (this.isPlaying && !this.isPaused) {
            if (this.audio) {
                this.audio.pause();
            }
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
            this.isPaused = true;
            this.isLoading = false;
        }
    },

    resume() {
        if (this.isPlaying && this.isPaused) {
            this.isPaused = false;
            if (this.audio && this.audio.src) {
                this.audio.play().catch(() => this.playCurrentChunk());
            } else {
                this.playCurrentChunk();
            }
        }
    },

    stop() {
        if (this.audio) {
            this.audio.pause();
            this.audio.removeAttribute('src');
            this.audio.load();
        }
        if (this.timer) {
            clearTimeout(this.timer);
            this.timer = null;
        }
        this.isPlaying = false;
        this.isPaused = false;
        this.isLoading = false;
        this.chunkIndex = 0;
        this.chunks = [];
        this.progress = '';
    },

    changeRate(newRate) {
        this.rate = parseFloat(newRate);
        if (this.audio) {
            this.audio.playbackRate = this.rate;
        }
    },

    updateProgress() {
        if (this.chunks.length > 0 && this.isPlaying) {
            this.progress = `${Math.min(this.chunkIndex + 1, this.chunks.length)} / ${this.chunks.length}`;
        } else {
            this.progress = '';
        }
    }
}" class="inline-flex flex-wrap items-center gap-2 p-2 bg-amber-50/90 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-xl shadow-2xs {{ $class }}">

    <!-- Play / Resume Button -->
    <template x-if="!isPlaying || isPaused">
        <button 
            type="button" 
            @click="play()"
            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-medium text-xs sm:text-sm rounded-lg shadow-xs transition-all duration-150 active:scale-95 cursor-pointer">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
            </svg>
            <span x-text="isPaused ? 'जारी रखें (Resume)' : '{{ $label }}'"></span>
        </button>
    </template>

    <!-- Pause Button -->
    <template x-if="isPlaying && !isPaused">
        <button 
            type="button" 
            @click="pause()"
            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-medium text-xs sm:text-sm rounded-lg shadow-xs transition-all duration-150 active:scale-95 cursor-pointer">
            <template x-if="isLoading">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </template>
            <template x-if="!isLoading">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </template>
            <span x-text="isLoading ? 'लोड हो रहा है...' : 'रोकें (Pause)'"></span>
        </button>
    </template>

    <!-- Stop Button -->
    <button 
        type="button" 
        x-show="isPlaying || isPaused"
        @click="stop()"
        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-medium text-xs sm:text-sm rounded-lg transition-all duration-150 cursor-pointer">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
            <rect x="5" y="5" width="10" height="10" rx="1.5" />
        </svg>
        <span>बंद करें</span>
    </button>

    <!-- Progress indicator with animated soundwave indicator -->
    <div x-show="progress" class="inline-flex items-center gap-1.5 px-2 py-1 bg-amber-100/80 dark:bg-amber-900/40 rounded-lg text-amber-900 dark:text-amber-200 text-xs font-mono">
        <span class="inline-flex items-center gap-0.5">
            <span class="w-1 h-3 bg-amber-600 rounded-full animate-pulse"></span>
            <span class="w-1 h-2 bg-amber-500 rounded-full animate-pulse delay-75"></span>
            <span class="w-1 h-3.5 bg-amber-600 rounded-full animate-pulse delay-150"></span>
        </span>
        <span class="font-medium text-[11px]" x-text="'वाक्य ' + progress"></span>
    </div>

    <!-- Speed Selector -->
    <div class="inline-flex items-center gap-1 text-xs text-slate-600 dark:text-slate-400 pl-1 border-l border-amber-200 dark:border-amber-800">
        <span class="text-[11px] font-medium">गति:</span>
        <button 
            type="button" 
            @click="changeRate(0.85)"
            :class="rate === 0.85 ? 'bg-amber-200 dark:bg-amber-900 text-amber-900 dark:text-amber-100 font-bold' : 'hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="px-1.5 py-0.5 rounded text-[11px] transition cursor-pointer">0.8x</button>
        <button 
            type="button" 
            @click="changeRate(1.0)"
            :class="rate === 1.0 ? 'bg-amber-200 dark:bg-amber-900 text-amber-900 dark:text-amber-100 font-bold' : 'hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="px-1.5 py-0.5 rounded text-[11px] transition cursor-pointer">1.0x</button>
        <button 
            type="button" 
            @click="changeRate(1.25)"
            :class="rate === 1.25 ? 'bg-amber-200 dark:bg-amber-900 text-amber-900 dark:text-amber-100 font-bold' : 'hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="px-1.5 py-0.5 rounded text-[11px] transition cursor-pointer">1.25x</button>
    </div>
</div>
