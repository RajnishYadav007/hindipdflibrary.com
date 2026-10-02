@props([
    'status' => 'pending_review',
    'createdAt' => null,
    'publishedAt' => null,
    'updatedAt' => null,
])

@php
    $statusStr = is_object($status) ? ($status->value ?? (string) $status) : (string) $status;
    $isPublished = $statusStr === 'published';
    $isRevision = $statusStr === 'revision_required';
    $isRejected = $statusStr === 'rejected';
    $isPending = in_array($statusStr, ['pending_review', 'under_review']);
    $isDraft = $statusStr === 'draft';
@endphp

<div class="rounded-2xl border border-slate-200/90 bg-white p-5 sm:p-6 shadow-2xs">
    <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-3">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 font-serif flex items-center gap-2">
            <x-app-icon name="clock" class="h-4 w-4 text-slate-400" />
            <span>प्रस्तुति समीक्षा स्थिति (Submission Timeline)</span>
        </h3>
        <span class="text-[11px] text-slate-400">वास्तविक समीक्षा चरण</span>
    </div>

    <!-- Timeline Steps -->
    <div class="relative grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-4">
        <!-- Step 1: Submitted -->
        <div class="flex items-start gap-3 relative">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 border border-emerald-300">
                <x-app-icon name="check-circle" class="h-4 w-4" />
            </div>
            <div>
                <div class="text-xs font-bold text-slate-900">प्रस्तुत (Submitted)</div>
                <div class="text-[11px] text-slate-500 mt-0.5">
                    {{ $createdAt ? $createdAt->format('d M Y, h:i A') : 'दिनांक उपलब्ध नहीं' }}
                </div>
                <div class="text-[10px] text-emerald-700 font-medium mt-0.5">✓ सफलतापूर्वक प्राप्त</div>
            </div>
        </div>

        <!-- Step 2: Editorial Review -->
        <div class="flex items-start gap-3 relative">
            @if ($isPending)
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700 border border-amber-300 animate-pulse">
                    <x-app-icon name="clock" class="h-4 w-4" />
                </div>
                <div>
                    <div class="text-xs font-bold text-amber-900">संपादकीय समीक्षा (In Review)</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">सामग्री की गुणवत्ता व कॉपीराइट जांच जारी</div>
                    <div class="text-[10px] text-amber-700 font-medium mt-0.5">⏳ वर्तमान चरण (Active)</div>
                </div>
            @elseif ($isDraft)
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 border border-slate-300">
                    <x-app-icon name="clock" class="h-4 w-4" />
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-700">प्रारूप (Draft)</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">समीक्षा हेतु जमा करना शेष</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">संपादन मोड</div>
                </div>
            @else
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 border border-emerald-300">
                    <x-app-icon name="check-circle" class="h-4 w-4" />
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900">संपादकीय समीक्षा पूर्ण</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">मूल्यांकन संपन्न</div>
                    <div class="text-[10px] text-emerald-700 font-medium mt-0.5">✓ मूल्यांकित</div>
                </div>
            @endif
        </div>

        <!-- Step 3: Final State / Published -->
        <div class="flex items-start gap-3 relative">
            @if ($isPublished)
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 border border-emerald-300">
                    <x-app-icon name="check-circle" class="h-4 w-4" />
                </div>
                <div>
                    <div class="text-xs font-bold text-emerald-900">वेबसाइट पर प्रकाशित (Live)</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">
                        {{ $publishedAt ? $publishedAt->format('d M Y') : 'स्वीकृत एवं लाइव' }}
                    </div>
                    <div class="text-[10px] text-emerald-700 font-medium mt-0.5">✓ सार्वजनिक रूप से उपलब्ध</div>
                </div>
            @elseif ($isRevision)
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-orange-100 text-orange-700 border border-orange-300">
                    <x-app-icon name="alert-circle" class="h-4 w-4" />
                </div>
                <div>
                    <div class="text-xs font-bold text-orange-900">संशोधन अपेक्षित (Revision)</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">संपादकीय सुझाव के अनुसार सुधारें</div>
                    <div class="text-[10px] text-orange-700 font-medium mt-0.5">⚠ कार्रवाई आवश्यक</div>
                </div>
            @elseif ($isRejected)
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-700 border border-rose-300">
                    <x-app-icon name="x-circle" class="h-4 w-4" />
                </div>
                <div>
                    <div class="text-xs font-bold text-rose-900">अस्वीकृत (Rejected)</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">दिशानिर्देशों के अनुरूप नहीं</div>
                    <div class="text-[10px] text-rose-700 font-medium mt-0.5">✕ समाप्त</div>
                </div>
            @else
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-400 border border-slate-200">
                    <x-app-icon name="clock" class="h-4 w-4 text-slate-300" />
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-400">प्रकाशन (Publication)</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">समीक्षा उपरांत स्वचालित प्रकाशन</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">प्रतीक्षारत</div>
                </div>
            @endif
        </div>
    </div>
</div>