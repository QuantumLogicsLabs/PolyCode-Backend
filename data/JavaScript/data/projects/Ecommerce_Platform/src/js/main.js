// Main application entry point
import { App } from './app/App.js';
import { Router } from './app/Router.js';
import { State } from './app/State.js';
import { AuthService } from './services/AuthService.js';
import { ProductService } from './services/ProductService.js';
import { CartService } from './services/CartService.js';

// Initialize application when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Create application instance
    const app = new App();
    
    // Initialize services
    const authService = new AuthService();
    const productService = new ProductService();
    const cartService = new CartService();
    
    // Initialize router
    const router = new Router();
    
    // Initialize state management
    const state = new State({
        user: null,
        products: [],
        cart: [],
        loading: false,
        error: null
    });
    
    // Configure router
    router.addRoute('/', () => app.renderHomePage());
    router.addRoute('/products', () => app.renderProductsPage());
    router.addRoute('/products/:id', (params) => app.renderProductPage(params.id));
    router.addRoute('/cart', () => app.renderCartPage());
    router.addRoute('/checkout', () => app.renderCheckoutPage());
    router.addRoute('/login', () => app.renderLoginPage());
    router.addRoute('/register', () => app.renderRegisterPage());
    router.addRoute('/profile', () => app.renderProfilePage());
    
    // Handle 404
    router.setNotFound(() => app.renderNotFoundPage());
    
    // Start application
    app.init({
        router,
        state,
        services: {
            auth: authService,
            product: productService,
            cart: cartService
        }
    });
    
    // Handle initial route
    router.handleRoute(window.location.pathname);
    
    // Handle browser back/forward
    window.addEventListener('popstate', (event) => {
        router.handleRoute(window.location.pathname);
    });
    
    // Handle navigation
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a');
        if (link && link.getAttribute('href')?.startsWith('/')) {
            event.preventDefault();
            const path = link.getAttribute('href');
            router.navigate(path);
        }
    });
    
    console.log('E-commerce Platform initialized successfully!');
});

// Global error handling
window.addEventListener('error', (event) => {
    console.error('Global error:', event.error);
    // In production, you might want to send this to an error tracking service
});

// Handle unhandled promise rejections
window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled promise rejection:', event.reason);
    event.preventDefault();
});