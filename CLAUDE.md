# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 8 application that integrates with the Epoint payment gateway system for processing payments in AZN (Azerbaijani Manat). The application also includes AI chat functionality using OpenAI's GPT and local Ollama models.

## Development Commands

### Setup
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file and configure
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate
```

### Development Workflow
```bash
# Start Laravel development server
php artisan serve

# Compile frontend assets (development)
npm run dev

# Watch for changes and recompile
npm run watch

# Production build
npm run production
```

### Testing
```bash
# Run all tests
vendor/bin/phpunit

# Run specific test suite
vendor/bin/phpunit --testsuite=Feature
vendor/bin/phpunit --testsuite=Unit

# Run a single test file
vendor/bin/phpunit tests/Feature/ExampleTest.php
```

### Artisan Commands
```bash
# Clear application cache
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# View all registered routes
php artisan route:list
```

## Architecture Overview

### Payment Integration Flow

The application implements a dual payment controller architecture:

1. **EpointController** (app/Http/Controllers/EpointController.php) - Main integration controller
   - `showPayForm()`: Displays payment form
   - `createPayment()`: Creates payment request to Epoint API with signature
   - `callback()`: Handles payment callbacks and updates user balances

2. **PaymentController** (app/Http/Controllers/PaymentController.php) - Alternative implementation
   - Uses base64-encoded JSON payloads with SHA1 signatures
   - Configured with public/private key authentication

**Payment Flow:**
- User submits amount via payment form
- Application creates order with unique ID
- Data and signature are sent to Epoint API (`/mock/api/1/request`)
- User is redirected to Epoint checkout page with payment token
- After payment, Epoint calls back to `/mock/callback`
- User balance is updated in database upon successful payment

### AI Chat Integration

The application integrates two different AI chat systems:

1. **OpenAI ChatGPT** (ChatController)
   - Uses GPT-4o-mini model
   - Maintains conversation history in session storage
   - API key stored in `OPENAI_API_KEY` environment variable

2. **Local Ollama** (OllamaController)
   - Connects to local Ollama instance at `http://localhost:11434`
   - Uses DeepSeek-R1 model
   - Non-streaming responses

### Configuration

**Epoint Configuration** (config/services.php):
```php
'epoint' => [
    'private_key' => env('EPOINT_PRIVATE_KEY'),
    'public_key' => env('EPOINT_PUBLIC_KEY'),
    'api_url' => env('EPOINT_API_URL'),
    'checkout_url' => env('EPOINT_CHECKOUT_URL'),
]
```

Add these to your `.env` file along with `OPENAI_API_KEY` for chat functionality.

### Database Schema

**Users Table Extensions:**
- `balance` (decimal 10,2) - User wallet balance, updated via payment callbacks

### Routing Structure

- **Web Routes** (routes/web.php): Payment forms, callbacks, chat interface, Ollama interface
- **API Routes** (routes/api.php): RESTful payment endpoints with Sanctum authentication

The application uses route model binding and contains a mock Epoint API endpoint for local testing (`/mock/api/1/request` and `/mock/callback`).

### Views Organization

- `pay.blade.php` - Payment form
- `redirect-to-epoint.blade.php` - Auto-submit form to Epoint checkout
- `success.blade.php` / `error.blade.php` - Payment result pages
- `chat.blade.php` - OpenAI chat interface
- `ollama_form.blade.php` / `ollama.blade.php` - Ollama chat interface
- `payment/` - Alternative payment implementation views
- `products/` - Product listing views

## Important Notes

- The application runs on `http://epoint-integration.test` in local development (configured in payment URLs)
- Payment signatures use form-data encoding, not JSON
- User balance updates are handled automatically in the payment callback
- Chat history is stored in session, not persisted to database
- The application includes mock Epoint endpoints for testing without external API access
