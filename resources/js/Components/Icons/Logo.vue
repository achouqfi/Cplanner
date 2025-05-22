<template>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 120" role="img" aria-label="Cplanner Analytics Logo">
  <!-- Analytics data point visualization - more prominent -->
  <circle cx="42" cy="50" r="12" fill="#3b82f6" />
  <circle cx="70" cy="70" r="12" fill="#3b82f6" />
  <circle cx="100" cy="30" r="12" fill="#3b82f6" />

  <!-- Connection lines representing data flow - thicker and more visible -->
  <line x1="42" y1="50" x2="70" y2="70" stroke="#3b82f6" stroke-width="6" />
  <line x1="70" y1="70" x2="100" y2="30" stroke="#3b82f6" stroke-width="6" />

  <!-- Text component - enhanced for header visibility -->
  <text x="140" y="70" font-family="Arial, sans-serif" font-weight="bold" font-size="60" fill="#262626">
    <tspan fill="#3b82f6" font-weight="900">C</tspan><tspan>planner</tspan>
  </text>

  <!-- Optional subtle glow effect for better visibility against varied backgrounds -->
  <filter id="glow" x="-20%" y="-20%" width="170%" height="140%">
    <feGaussianBlur stdDeviation="2" result="blur" />
    <feComposite in="SourceGraphic" in2="blur" operator="over" />
  </filter>
</svg>
</template>
