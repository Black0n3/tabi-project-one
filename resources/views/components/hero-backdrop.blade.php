@props(['image' => null, 'compact' => false])

{{-- Animated "dusk render" backdrop: layered navy towers with a window grid and flickering
     lit windows. Falls back to a blurred project cover photo when one is available. --}}
<div class="absolute inset-0 overflow-hidden bg-navy-950" aria-hidden="true">
    <div class="absolute inset-0 bg-gradient-to-br from-navy-950 via-navy-900 to-navy-800"></div>

    @if ($image)
        <img src="{{ $image }}" alt="" class="absolute inset-0 h-full w-full object-cover scale-110 blur-sm saturate-[.9] opacity-50">
    @else
        <div class="absolute -inset-[6%] anim-drift" style="filter: blur(1.5px);">
            {{-- dusk glow behind the skyline --}}
            <div class="absolute right-[8%] top-[18%] h-80 w-80 rounded-full blur-[50px]" style="background: radial-gradient(circle, rgba(255,170,90,.22), transparent 70%);"></div>
            <div class="absolute right-[34%] -top-10 h-64 w-64 rounded-full blur-[40px]" style="background: radial-gradient(circle, rgba(127,176,255,.18), transparent 70%);"></div>

            <div class="absolute bottom-0 right-[760px] hidden lg:block w-[170px] opacity-60 mullions border-t border-white/10" style="height: {{ $compact ? 200 : 320 }}px; background-color: #0D2552;"></div>

            <div class="absolute bottom-0 right-[300px] hidden md:block w-[210px] mullions border-t border-white/15" style="height: {{ $compact ? 240 : 420 }}px; background-color: #102B60;">
                <span class="absolute left-[22px] top-[60px] h-3.5 w-5 window-glow anim-twinkle-b"></span>
                <span class="absolute left-[90px] top-[150px] h-3.5 w-5 window-glow anim-twinkle-c"></span>
                <span class="absolute left-[150px] top-[220px] h-3.5 w-5 window-glow anim-twinkle"></span>
            </div>

            <div class="absolute bottom-0 right-[40px] sm:right-[520px] w-[150px] sm:w-[250px] mullions border-t border-white/20" style="height: {{ $compact ? 280 : 580 }}px; background: linear-gradient(180deg, #17356F, #0C2250);">
                <span class="absolute left-5 top-16 h-4 w-[22px] window-glow anim-twinkle"></span>
                <span class="absolute left-16 top-24 h-4 w-[22px] window-glow anim-twinkle-c"></span>
                <span class="absolute left-[150px] top-40 h-4 w-[22px] window-glow anim-twinkle-b"></span>
                <span class="absolute left-[108px] top-56 h-4 w-[22px] window-glow anim-twinkle"></span>
                <span class="absolute left-5 top-72 h-4 w-[22px] window-glow anim-twinkle-c"></span>
            </div>

            <div class="absolute bottom-0 right-[20px] hidden xl:block w-[290px] mullions border-t border-white/15" style="height: {{ $compact ? 220 : 480 }}px; background-color: #123068;">
                <span class="absolute left-[30px] top-20 h-3.5 w-5 window-glow anim-twinkle-c"></span>
                <span class="absolute left-[100px] top-[150px] h-3.5 w-5 window-glow anim-twinkle"></span>
                <span class="absolute left-[200px] top-[110px] h-3.5 w-5 window-glow anim-twinkle-b"></span>
            </div>

            <div class="absolute inset-x-0 bottom-0 h-44" style="background: radial-gradient(ellipse at 70% 100%, rgba(127,176,255,.22), transparent 70%);"></div>
        </div>
    @endif

    <div class="absolute inset-0 bg-gradient-to-r from-navy-950/95 via-navy-950/70 via-45% to-transparent"></div>
    <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-navy-950/60 to-transparent"></div>
</div>
