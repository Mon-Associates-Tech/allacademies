import './bootstrap';

import {gsap} from 'gsap';
import 'chart.js';

import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

gsap.from(".article", {
    y: 100,               // Start each card 100px below its natural position
    opacity: 0,           // Start with opacity at 0 for a fade-in effect
    duration: 1,          // Duration of each card animation
    ease: "power3.out",   // Ease type for smoothness
    stagger: 0.3,         // Delay each card by 0.3s
    scrollTrigger: {
        trigger: ".article-container", // Element that triggers the scroll
        start: "top 80%",           // Animation starts when trigger is 80% from top of viewport
        toggleActions: "restart none none none" // Plays only once
    }
});


gsap.from(".dl", {
    y: 100,               // Start each card 100px below its natural position
    opacity: 0,           // Start with opacity at 0 for a fade-in effect
    duration: 1,          // Duration of each card animation
    ease: "power3.out",   // Ease type for smoothness
    stagger: 0.3,         // Delay each card by 0.3s
    scrollTrigger: {
        trigger: ".dl-container", // Element that triggers the scroll
        start: "top 80%",           // Animation starts when trigger is 80% from top of viewport
        toggleActions: "restart none none none" // Plays only once
    }
});

gsap.from(".feature-item.left", {
    x: -200,                  // Start 200px to the left of its original position
    opacity: 0,               // Start with opacity at 0
    duration: 1.5,            // Duration of each animation
    ease: "bounce.out",       // Bounce easing for entry effect
    stagger: 0.3,             // Delay between each item in the left set
    scrollTrigger: {
        trigger: ".feature-grid",
        start: "top 80%",
        toggleActions: "restart none none none", // Restarts animation each time it enters viewport
    }
});

gsap.from(".feature-item.right", {
    x: 200,                   // Start 200px to the right of its original position
    opacity: 0,               // Start with opacity at 0
    duration: 1.5,            // Duration of each animation
    ease: "bounce.out",       // Bounce easing for entry effect
    stagger: 0.3,             // Delay between each item in the right set
    scrollTrigger: {
        trigger: ".feature-grid",
        start: "top 80%",
        toggleActions: "restart none none none", // Restarts animation each time it enters viewport
    }
});



gsap.from(".price-card", {
    y: 100,                // Start each price-card 100px below its natural position
    opacity: 0,            // Start with opacity at 0 for a fade-in effect
    duration: 1,           // Duration of each price-card animation
    ease: "power3.out",    // Ease type for smoothness
    stagger: {
        each: 0.3,         // Stagger each animation by 0.3s
        from: "center"     // Start stagger from the middle price-card
    },
    scrollTrigger: {
        trigger: ".price-card-container", // Element that triggers the scroll
        start: "top 80%",           // Animation starts when trigger is 80% from top of viewport
        toggleActions: "restart none none none", // Restart animation every time it enters the viewport
    }
});


gsap.from(".fade-in-text", {
    y: 50,                  // Move text from 50px below
    opacity: 0,             // Start with opacity at 0 for fade-in effect
    duration: 1,            // Duration of the animation
    ease: "power2.out",     // Smooth ease for a subtle fade-in
    scrollTrigger: {
        trigger: ".fade-in-text", // Trigger animation when this element enters viewport
        start: "top 80%",        // Start when top of element is 80% from top of viewport
        toggleActions: "play none none none", // Play only once
    }
});
