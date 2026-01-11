# ChecksTokenAvailability Trait

## Overview
Reusable trait for checking token availability across Livewire components, controllers, and services.

## Usage

### In Livewire Components

```php
use App\Traits\ChecksTokenAvailability;

class AcademicChat extends Component
{
    use ChecksTokenAvailability;
    
    public $canSendMessage = true;
    public $tokenWarningMessage;
    
    public function mount()
    {
        $result = $this->checkTokenAvailability();
        $this->canSendMessage = $result['available'];
        $this->tokenWarningMessage = $result['message'];
    }
    
    public function sendMessage()
    {
        if (!$this->canSendMessage(200)) {
            $this->addError('message', 'Insufficient tokens');
            return;
        }
        
        // Process message...
    }
}
```

### In Controllers

```php
use App\Traits\ChecksTokenAvailability;

class ChatController extends Controller
{
    use ChecksTokenAvailability;
    
    public function store(Request $request)
    {
        $result = $this->checkTokenAvailability(500);
        
        if (!$result['available']) {
            return response()->json([
                'error' => $result['message'],
                'cycle' => $result['cycle']
            ], 403);
        }
        
        // Process request...
    }
}
```

### In Services

```php
use App\Traits\ChecksTokenAvailability;

class AcademicChatService
{
    use ChecksTokenAvailability;
    
    public function generateResponse($message)
    {
        $check = $this->checkTokenAvailability(1000);
        
        if (!$check['available']) {
            throw new InsufficientTokensException($check['message']);
        }
        
        // Generate response...
    }
}
```

## Methods

### checkTokenAvailability(int $requiredTokens = 200): array

Returns detailed token availability information.

**Parameters:**
- `$requiredTokens` - Minimum tokens required (default: 200)

**Returns:**
```php
[
    'available' => bool,      // Whether tokens are available
    'message' => string|null, // Error message if not available
    'cycle' => SubscriptionCycle|null // Current cycle object
]
```

**Possible messages:**
- `'no_user'` - No authenticated user
- `'no_subscription'` - No active subscription cycle
- `'expired'` - Subscription cycle has expired
- `'depleted'` - All tokens used
- `'insufficient'` - Not enough tokens for required amount
- `null` - Tokens available

### canSendMessage(int $requiredTokens = 200): bool

Quick boolean check for token availability.

**Returns:** `true` if tokens available, `false` otherwise

### getTokenWarningMessage(int $requiredTokens = 200): ?string

Get the warning message without full details.

**Returns:** Warning message string or `null` if available

## Example: Custom Required Tokens

```php
// Check for 500 tokens
$result = $this->checkTokenAvailability(500);

// Quick check for 1000 tokens
if ($this->canSendMessage(1000)) {
    // Process large request
}

// Get warning for 100 tokens
$warning = $this->getTokenWarningMessage(100);
```

## Benefits

1. **Consistent Logic** - Same token checking across entire app
2. **Reusable** - Use in any class (components, controllers, services)
3. **Flexible** - Customize required token amount
4. **Detailed** - Returns cycle object for additional context
5. **Clean** - Removes duplicate code
