@props(['image' => null, 'compact' => false])

<div class="absolute inset-0 overflow-hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-gradient-to-br from-[#050505] via-[#0B0B0B] to-[#131313]"></div>

    @if ($image)
        <img src="{{ $image }}" alt="" class="absolute inset-0 h-full w-full object-cover scale-110 blur-md saturate-[.85] opacity-60">
    @else
        <div class="absolute -inset-[6%] anim-drift" style="filter: blur(2.5px);">
            <div class="absolute right-[120px] top-[60px] h-64 w-64 rounded-full blur-[30px]" style="background: radial-gradient(circle, rgba(255,255,255,.09), transparent 70%);"></div>

            <div class="absolute bottom-0 right-[760px] hidden lg:block w-[170px] opacity-55 mullions border-t-2 border-white/10" style="height: {{ $compact ? 220 : 330 }}px; background-color: #121212;"></div>

            <div class="absolute bottom-0 right-[300px] hidden md:block w-[210px] mullions border-t-2 border-white/10" style="height: {{ $compact ? 260 : 430 }}px; background-color: #151515;">
                <span class="absolute left-[22px] top-[60px] h-3.5 w-5 window-glow anim-twinkle-b"></span>
                <span class="absolute left-[90px] top-[150px] h-3.5 w-5 window-glow anim-twinkle-c"></span>
                <span class="absolute left-[150px] top-[220px] h-3.5 w-5 window-glow anim-twinkle"></span>
            </div>

            <div class="absolute bottom-0 right-[40px] sm:right-[520px] w-[150px] sm:w-[250px] mullions border-t-2 border-white/10" style="height: {{ $compact ? 300 : 600 }}px; background: linear-gradient(180deg, #1c1c1c, #0a0a0a);">
                <span class="absolute left-5 top-16 h-4 w-[22px] window-glow anim-twinkle"></span>
                <span class="absolute left-16 top-24 h-4 w-[22px] window-glow anim-twinkle-c"></span>
                <span class="absolute left-[150px] top-40 h-4 w-[22px] window-glow anim-twinkle-b"></span>
                <span class="absolute left-[108px] top-56 h-4 w-[22px] window-glow anim-twinkle"></span>
                <span class="absolute left-5 top-72 h-4 w-[22px] window-glow anim-twinkle-c"></span>
            </div>

            <div class="absolute bottom-0 right-[20px] hidden xl:block w-[290px] mullions border-t-2 border-white/10" style="height: {{ $compact ? 240 : 500 }}px; background-color: #141414;">
                <span class="absolute left-[30px] top-20 h-3.5 w-5 window-glow anim-twinkle-c"></span>
                <span class="absolute left-[100px] top-[150px] h-3.5 w-5 window-glow anim-twinkle"></span>
                <span class="absolute left-[200px] top-[110px] h-3.5 w-5 window-glow anim-twinkle-b"></span>
            </div>

            <div class="absolute inset-x-0 bottom-0 h-44" style="background: radial-gradient(ellipse at 70% 100%, rgba(255,255,255,.08), transparent 70%);"></div>
        </div>
    @endif

    <div class="absolute inset-0 bg-gradient-to-r from-[#050505]/95 via-[#050505]/85 via-40% to-[#050505]/25"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[#050505]/60"></div>
    <div class="absolute inset-0 opacity-[0.05]" style="background-image: repeating-linear-gradient(45deg, white 0, white 1px, transparent 1px, transparent 26px);"></div>
</div>
