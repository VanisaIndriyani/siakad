@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-white/15 bg-white/5 text-white placeholder:text-white/50 focus:border-emerald-400 focus:ring-emerald-400 rounded-xl shadow-sm']) }}>
