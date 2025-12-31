# Hoàng Phúc 68 - Vietnamese New Year Gaming Platform

## Overview

This is a Vietnamese-language web application themed around Lunar New Year (Tết) celebrations. The platform serves as a gaming hub featuring multiple prediction-based mini-games with a festive red and gold aesthetic. The application is built as a static frontend website with HTML, CSS, and JavaScript, designed primarily for mobile devices.

The main entry point (`index.html`) displays a landing card with Tết-themed visuals (falling envelopes animation) and links to various gaming tools located in the `/games` directory.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture
- **Technology**: Pure HTML5, CSS3, and vanilla JavaScript
- **Design Pattern**: Multi-page static site architecture
- **Styling Approach**: Inline CSS within each HTML file using CSS custom properties (variables) for theming
- **Mobile-First Design**: Viewport meta tags configured to prevent scaling (`maximum-scale=1.0, user-scalable=no`) for app-like experience

### Page Structure
| File | Purpose |
|------|---------|
| `index.html` | Main landing page with game selection grid and festive animations |
| `games/68gbsic.html` | Sicbo dice game prediction tool |
| `games/b52dudoan.html` | B52 game prediction interface |
| `games/hitdudoan.html` | HITCLUB game prediction interface |
| `games/xanh68gb.html` | "Green Table" (Bàn Xanh) AI prediction tool for 68GB |

### Design Patterns
- **CSS Variables**: Used for consistent theming (Tết colors: red `#d90429`, gold `#ffcf33`)
- **Responsive Containers**: Fixed-width cards centered on screen with percentage-based positioning
- **Animation**: CSS keyframe animations for falling envelope effects
- **Gradient Backgrounds**: Multi-color gradients for visual appeal in game interfaces

### UI Components
- Prediction display boxes with circular indicators
- Interactive buttons styled for touch targets
- Status text areas for game state feedback
- Embedded iframes for game content (in some tools)

## External Dependencies

### CDN Resources
| Resource | Purpose |
|----------|---------|
| Font Awesome 6.4.0 | Icon library via cdnjs |
| Google Fonts - Montserrat | Primary typography for main site |
| Google Fonts - Inter | Typography for game tools |
| Google Fonts - Orbitron | Futuristic font for specific game interfaces |
| DotLottie Player | Animation player component (used in Sicbo tool) |

### External Media
- Background images hosted on PostImg (`i.postimg.cc`)
- Favicon hosted on PostImg
- Background wallpaper for games from PostImg

### No Backend Dependencies
- This is a purely client-side application
- No database integration
- No server-side processing
- No authentication system
- All game logic runs in the browser