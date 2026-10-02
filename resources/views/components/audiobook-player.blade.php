@props([
    'audiobook',
    'chapters' => null,
])

@php
    // Use normalized playable tracks (supports Case A multi-chapter, Case B single-track, Case D empty)
    $tracks = $audiobook->playable_tracks;
    $hasPlayableSource = $audiobook->hasPlayableSource();
    $isSingleTrack = $tracks->count() === 1 && $tracks->first()['id'] === 'single';
@endphp

<div x-data="{
    tracks: {{ Js::from($tracks) }},
    currentIndex: 0,
    playbackState: '{{ $hasPlayableSource ? 'IDLE' : 'NO_SOURCE' }}', // NO_SOURCE, IDLE, LOADING, READY, PLAYING, PAUSED, ENDED, ERROR
    isPlaying: false,
    currentTime: 0,
    duration: 0,
    playbackRate: 1.0,
    volume: 1.0,
    isMuted: false,
    showPlaylist: false,
    errorMessage: null,
    audio: null,
    isSingleTrack: {{ $isSingleTrack ? 'true' : 'false' }},

    init() {
        this.audio = this.$refs.audioElement;
        if (!this.audio) return;

        if (this.tracks.length === 0 || !this.hasValidSource(0)) {
            this.playbackState = 'NO_SOURCE';
            return;
        }

        // Initialize audio event listeners
        this.setupAudioListeners();

        // Preload first track metadata
        this.loadTrack(0, false);
    },

    get currentTrack() {
        return this.tracks[this.currentIndex] || null;
    },

    hasValidSource(index) {
        return Boolean(this.tracks[index] && this.tracks[index].audio_url);
    },

    setupAudioListeners() {
        this.audio.addEventListener('loadstart', () => {
            if (this.playbackState !== 'NO_SOURCE') {
                this.playbackState = 'LOADING';
                this.errorMessage = null;
            }
        });

        this.audio.addEventListener('loadedmetadata', () => {
            if (this.audio.duration && !isNaN(this.audio.duration)) {
                this.duration = this.audio.duration;
            }
            if (this.playbackState === 'LOADING') {
                this.playbackState = 'READY';
            }
        });

        this.audio.addEventListener('canplay', () => {
            if (this.playbackState === 'LOADING') {
                this.playbackState = 'READY';
            }
        });

        this.audio.addEventListener('playing', () => {
            this.playbackState = 'PLAYING';
            this.isPlaying = true;
            this.errorMessage = null;
            this.dispatchAnalytics('chapter_play');
        });

        this.audio.addEventListener('pause', () => {
            if (this.playbackState === 'PLAYING') {
                this.playbackState = 'PAUSED';
            }
            this.isPlaying = false;
        });

        this.audio.addEventListener('timeupdate', () => {
            this.currentTime = this.audio.currentTime || 0;
            if (this.audio.duration && !isNaN(this.audio.duration)) {
                this.duration = this.audio.duration;
            }
        });

        this.audio.addEventListener('ended', () => {
            this.playbackState = 'ENDED';
            this.isPlaying = false;
            this.nextChapter(true);
        });

        this.audio.addEventListener('error', (e) => {
            console.error('Audio playback error', e);
            this.playbackState = 'ERROR';
            this.isPlaying = false;
            this.errorMessage = 'ऑडियो लोड करने में समस्या हुई। कृपया पुनः प्रयास करें।';
        });
    },

    loadTrack(index, autoPlay = true) {
        if (index < 0 || index >= this.tracks.length) return;
        
        // Clean up prior track playback to avoid ghost audio
        if (this.audio.src) {
            this.audio.pause();
            this.audio.currentTime = 0;
        }

        this.currentIndex = index;
        const track = this.tracks[index];

        if (!track || !track.audio_url) {
            this.playbackState = 'NO_SOURCE';
            this.isPlaying = false;
            return;
        }

        this.playbackState = 'LOADING';
        this.errorMessage = null;
        this.currentTime = 0;
        this.duration = track.duration_seconds || 0;

        this.audio.src = track.audio_url;
        this.audio.playbackRate = this.playbackRate;
        this.audio.volume = this.isMuted ? 0 : this.volume;

        if (autoPlay) {
            this.audio.play().catch(err => {
                console.warn('Autoplay prevented or failed:', err);
                this.playbackState = 'READY';
                this.isPlaying = false;
            });
        }
    },

    togglePlay() {
        if (this.playbackState === 'NO_SOURCE' || !this.hasValidSource(this.currentIndex)) {
            return;
        }

        if (!this.audio.src) {
            this.loadTrack(this.currentIndex, true);
            return;
        }

        if (this.isPlaying) {
            this.audio.pause();
        } else {
            this.audio.play().catch(err => {
                console.error('Play prevented:', err);
                this.playbackState = 'ERROR';
                this.errorMessage = 'ऑडियो चलाने में त्रुटि हुई। कृपया नेटवर्क कनेक्शन जांचें।';
            });
        }
    },

    prevChapter() {
        if (this.currentIndex > 0) {
            this.loadTrack(this.currentIndex - 1, true);
        }
    },

    nextChapter(isAuto = false) {
        if (this.currentIndex < this.tracks.length - 1) {
            this.loadTrack(this.currentIndex + 1, true);
        } else {
            this.isPlaying = false;
            this.playbackState = 'ENDED';
            if (isAuto) {
                this.dispatchAnalytics('audiobook_completed');
            }
        }
    },

    seek(e) {
        if (this.playbackState === 'NO_SOURCE' || !this.duration) return;
        const val = parseFloat(e.target.value);
        this.audio.currentTime = val;
        this.currentTime = val;
    },

    seekBy(deltaSeconds) {
        if (this.playbackState === 'NO_SOURCE' || !this.duration) return;
        const target = Math.max(0, Math.min(this.duration, (this.audio.currentTime || 0) + deltaSeconds));
        this.audio.currentTime = target;
        this.currentTime = target;
    },

    setSpeed(speed) {
        this.playbackRate = speed;
        if (this.audio) {
            this.audio.playbackRate = speed;
        }
    },

    toggleMute() {
        this.isMuted = !this.isMuted;
        if (this.audio) {
            this.audio.muted = this.isMuted;
        }
    },

    setVolume(val) {
        this.volume = parseFloat(val);
        if (this.audio) {
            this.audio.volume = this.volume;
        }
        if (this.volume > 0) this.isMuted = false;
    },

    formatTime(seconds) {
        if (!seconds || isNaN(seconds) || seconds <= 0) {
            return '00:00';
        }
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = Math.floor(seconds % 60);
        if (h > 0) {
            return `${h}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        }
        return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    },

    get formattedTotalDuration() {
        if (this.playbackState === 'NO_SOURCE') {
            return '--:--';
        }
        if (this.duration > 0) {
            return this.formatTime(this.duration);
        }
        if (this.currentTrack && this.currentTrack.duration_seconds > 0) {
            return this.formatTime(this.currentTrack.duration_seconds);
        }
        return 'अवधि उपलब्ध नहीं';
    },

    dispatchAnalytics(eventName) {
        if (window.fetch && this.currentTrack && this.currentTrack.id !== 'single') {
            fetch('/api/analytics/event', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                },
                body: JSON.stringify({
                    event: eventName,
                    audiobook_id: {{ $audiobook->id }},
                    chapter_id: this.currentTrack.id,
                })
            }).catch(() => {});
        }
    }
}"
@keydown.window="
    if (['INPUT', 'TEXTAREA'].includes($event.target.tagName)) return;
    if ($event.code === 'Space') {
        $event.preventDefault();
        togglePlay();
    } else if ($event.code === 'ArrowLeft') {
        seekBy(-5);
    } else if ($event.code === 'ArrowRight') {
        seekBy(5);
    }
"
class="relative w-full select-none">

    <!-- Hidden Native Audio Element (Range request & streaming enabled) -->
    <audio x-ref="audioElement" preload="metadata"></audio>

    <!-- Full Main Audiobook Player Card -->
    <div class="bg-gradient-to-br from-neutral-900 via-neutral-900 to-amber-950 text-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-neutral-800 relative overflow-hidden">
        
        <!-- Subtle Ambient Glow -->
        <div class="absolute -right-20 -top-20 w-60 h-60 bg-amber-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Missing Source Alert Banner (Case D) -->
        <template x-if="playbackState === 'NO_SOURCE'">
            <div class="mb-5 p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center gap-3 text-xs text-amber-200">
                <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>इस ऑडियोबुक का ऑडियो स्रोत अभी उपलब्ध नहीं है। हमारी टीम इसका वाचन संकलित कर रही है।</span>
            </div>
        </template>

        <!-- Playback Error Banner -->
        <template x-if="playbackState === 'ERROR' && errorMessage">
            <div class="mb-5 p-3.5 rounded-2xl bg-rose-500/20 border border-rose-500/40 flex items-center gap-3 text-xs text-rose-200">
                <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-text="errorMessage"></span>
            </div>
        </template>

        <div class="flex flex-col md:flex-row items-center gap-6 sm:gap-8">
            
            <!-- Cover Art / Placeholder -->
            <div class="w-32 h-44 sm:w-40 sm:h-56 shrink-0 rounded-2xl overflow-hidden shadow-xl border border-white/10 bg-neutral-800 relative group">
                @if($audiobook->cover_url)
                    <img src="{{ $audiobook->cover_url }}" alt="{{ $audiobook->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center p-4 text-center bg-gradient-to-t from-amber-900/80 to-neutral-800">
                        <svg class="w-12 h-12 text-amber-400 mb-2 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z" />
                        </svg>
                        <span class="text-xs font-semibold text-neutral-300">{{ $audiobook->title }}</span>
                    </div>
                @endif
            </div>

            <!-- Track Info & Controls -->
            <div class="flex-1 w-full flex flex-col justify-between">
                
                <!-- Track Meta -->
                <div class="mb-4">
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                            {{ $isSingleTrack ? 'एकल ट्रैक (Single Track)' : 'ऑडियोबुक (Audiobook)' }}
                        </span>
                        @if($audiobook->narrator)
                            <span class="text-xs text-neutral-400">
                                स्वर (Narrator): <strong class="text-neutral-200">{{ $audiobook->narrator }}</strong>
                            </span>
                        @endif
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight line-clamp-1">
                        {{ $audiobook->title }}
                    </h2>
                    <p class="text-sm text-amber-200/90 mt-1 font-medium flex items-center gap-1.5">
                        <template x-if="playbackState === 'NO_SOURCE'">
                            <span>ऑडियो स्रोत उपलब्ध नहीं है</span>
                        </template>
                        <template x-if="playbackState !== 'NO_SOURCE' && currentTrack">
                            <span x-text="isSingleTrack ? currentTrack.title : `${currentTrack.title}`"></span>
                        </template>
                    </p>
                </div>

                <!-- Progress Bar & Time -->
                <div class="mb-5">
                    <div class="flex items-center justify-between text-xs text-neutral-400 font-mono mb-1.5">
                        <span x-text="playbackState === 'NO_SOURCE' ? '--:--' : formatTime(currentTime)">00:00</span>
                        <span x-text="formattedTotalDuration">--:--</span>
                    </div>
                    <input 
                        type="range" 
                        min="0" 
                        :max="duration || 100" 
                        :value="currentTime" 
                        @input="seek($event)"
                        :disabled="playbackState === 'NO_SOURCE' || !duration"
                        class="w-full h-1.5 bg-neutral-700 rounded-lg appearance-none cursor-pointer accent-amber-500 disabled:opacity-40 disabled:cursor-not-allowed focus:outline-none">
                </div>

                <!-- Player Main Controls -->
                <div class="flex flex-wrap items-center justify-between gap-4">
                    
                    <!-- Left: Speed Selector -->
                    <div class="flex items-center gap-1 bg-neutral-800/80 rounded-xl p-1 border border-neutral-700 text-xs">
                        <button type="button" @click="setSpeed(0.8)" :class="playbackRate === 0.8 ? 'bg-amber-600 text-white' : 'text-neutral-400 hover:text-white'" class="px-2 py-1 rounded-lg font-medium transition">0.8x</button>
                        <button type="button" @click="setSpeed(1.0)" :class="playbackRate === 1.0 ? 'bg-amber-600 text-white' : 'text-neutral-400 hover:text-white'" class="px-2 py-1 rounded-lg font-medium transition">1.0x</button>
                        <button type="button" @click="setSpeed(1.25)" :class="playbackRate === 1.25 ? 'bg-amber-600 text-white' : 'text-neutral-400 hover:text-white'" class="px-2 py-1 rounded-lg font-medium transition">1.25x</button>
                        <button type="button" @click="setSpeed(1.5)" :class="playbackRate === 1.5 ? 'bg-amber-600 text-white' : 'text-neutral-400 hover:text-white'" class="px-2 py-1 rounded-lg font-medium transition">1.5x</button>
                    </div>

                    <!-- Center: Playback Buttons -->
                    <div class="flex items-center gap-3">
                        <!-- Prev Chapter -->
                        <button 
                            type="button" 
                            @click="prevChapter()" 
                            :disabled="playbackState === 'NO_SOURCE' || isSingleTrack || currentIndex === 0"
                            class="p-2.5 rounded-full text-neutral-300 hover:text-white hover:bg-neutral-800 disabled:opacity-30 disabled:cursor-not-allowed transition"
                            title="पिछला अध्याय (Previous Chapter)">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.445 14.832A1 1 0 0010 14v-2.798l5.445 3.63A1 1 0 0017 14V6a1 1 0 00-1.555-.832L10 8.798V6a1 1 0 00-1.555-.832l-6 4a1 1 0 000 1.664l6 4z"/>
                            </svg>
                        </button>

                        <!-- Main Play/Pause Button -->
                        <button 
                            type="button" 
                            @click="togglePlay()"
                            :disabled="playbackState === 'NO_SOURCE'"
                            class="w-14 h-14 rounded-full bg-gradient-to-tr from-amber-500 to-amber-400 text-neutral-950 font-bold flex items-center justify-center shadow-lg shadow-amber-500/30 hover:scale-105 active:scale-95 disabled:opacity-40 disabled:scale-100 disabled:cursor-not-allowed transition-all"
                            :title="playbackState === 'NO_SOURCE' ? 'ऑडियो अनुपलब्ध' : (isPlaying ? 'रोकें (Pause)' : 'चलाएं (Play)')">
                            
                            <!-- Loading Spinner -->
                            <template x-if="playbackState === 'LOADING'">
                                <svg class="w-6 h-6 animate-spin text-neutral-950" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>

                            <!-- Play Icon -->
                            <template x-if="playbackState !== 'LOADING' && !isPlaying">
                                <svg class="w-7 h-7 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </template>

                            <!-- Pause Icon -->
                            <template x-if="playbackState !== 'LOADING' && isPlaying">
                                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                                </svg>
                            </template>
                        </button>

                        <!-- Next Chapter -->
                        <button 
                            type="button" 
                            @click="nextChapter()" 
                            :disabled="playbackState === 'NO_SOURCE' || isSingleTrack || currentIndex >= tracks.length - 1"
                            class="p-2.5 rounded-full text-neutral-300 hover:text-white hover:bg-neutral-800 disabled:opacity-30 disabled:cursor-not-allowed transition"
                            title="अगला अध्याय (Next Chapter)">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4.555 5.168A1 1 0 003 6v8a1 1 0 001.555.832L10 11.202V14a1 1 0 001.555.832l6-4a1 1 0 000-1.664l-6-4A1 1 0 0010 6v2.798L4.555 5.168z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Right: Playlist Toggle & Volume -->
                    <div class="flex items-center gap-3">
                        <button 
                            type="button" 
                            @click="toggleMute()" 
                            class="text-neutral-400 hover:text-white transition"
                            title="म्यूट / अनम्यूट">
                            <template x-if="isMuted || volume === 0">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM12.293 7.293a1 1 0 011.414 0L15 8.586l1.293-1.293a1 1 0 111.414 1.414L16.414 10l1.293 1.293a1 1 0 01-1.414 1.414L15 11.414l-1.293 1.293a1 1 0 01-1.414-1.414L13.586 10l-1.293-1.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </template>
                            <template x-if="!isMuted && volume > 0">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z" clip-rule="evenodd"/>
                                </svg>
                            </template>
                        </button>

                        @if(!$isSingleTrack && $tracks->count() > 1)
                            <button 
                                type="button" 
                                @click="showPlaylist = !showPlaylist"
                                :class="showPlaylist ? 'bg-amber-500 text-neutral-950 font-bold' : 'bg-neutral-800 text-neutral-300 hover:text-white'"
                                class="px-3 py-1.5 rounded-xl text-xs flex items-center gap-1.5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                                <span>अध्याय सूची (<span x-text="tracks.length">{{ $tracks->count() }}</span>)</span>
                            </button>
                        @endif
                    </div>

                </div>

            </div>

        </div>

        <!-- Collapsible Chapter Playlist -->
        @if(!$isSingleTrack && $tracks->count() > 1)
            <div x-show="showPlaylist" x-collapse class="mt-6 pt-6 border-t border-neutral-800">
                <h3 class="text-sm font-semibold text-neutral-300 mb-3">सभी अध्याय (Chapters):</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-64 overflow-y-auto pr-1">
                    <template x-for="(track, idx) in tracks" :key="track.id">
                        <button 
                            type="button" 
                            @click="loadTrack(idx, true)"
                            :class="currentIndex === idx ? 'bg-amber-500/20 border-amber-500/50 text-amber-300' : 'bg-neutral-800/60 border-neutral-700/60 text-neutral-300 hover:bg-neutral-800'"
                            class="flex items-center justify-between p-3 rounded-xl border text-left text-xs transition">
                            <div class="flex items-center gap-2.5 truncate">
                                <span class="w-6 h-6 rounded-full bg-neutral-700/80 flex items-center justify-center font-mono text-[11px] shrink-0" x-text="track.chapter_number"></span>
                                <span class="font-medium truncate" x-text="track.title"></span>
                            </div>
                            <span class="font-mono text-neutral-400 shrink-0 ml-2" x-text="track.formatted_duration || ''"></span>
                        </button>
                    </template>
                </div>
            </div>
        @endif

    </div>

    <!-- Mobile Sticky Mini-Player -->
    <div 
        x-show="isPlaying"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-full opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-full opacity-0"
        class="fixed bottom-3 inset-x-3 sm:bottom-4 sm:right-4 sm:left-auto sm:w-96 z-50 bg-neutral-900/95 backdrop-blur-md text-white p-3 rounded-2xl shadow-2xl border border-amber-500/30 flex items-center gap-3">
        
        <div class="w-10 h-10 rounded-xl bg-amber-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-white animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
            </svg>
        </div>

        <div class="flex-1 min-w-0">
            <h4 class="text-xs font-bold text-white truncate">{{ $audiobook->title }}</h4>
            <p class="text-[11px] text-amber-300 truncate" x-text="currentTrack ? currentTrack.title : ''"></p>
        </div>

        <button 
            type="button" 
            @click="togglePlay()"
            class="p-2 rounded-full bg-amber-500 text-neutral-950 shrink-0">
            <template x-if="!isPlaying">
                <svg class="w-4 h-4 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </template>
            <template x-if="isPlaying">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                </svg>
            </template>
        </button>
    </div>

</div>
