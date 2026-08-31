# Reusable Modal Components

## Location
`resources/views/components/examination-hub/modals/`

## Available Components

Each modal component is a self-contained, reusable Blade component that can be included in any view using standard component syntax.

### 1. Message Modal
**File:** `message-modal.blade.php`

**Usage:**
```blade
<x-examination-hub.modals.message-modal />
```

**Data Requirements:**
```javascript
// Alpine.js data properties
showMessageModal: false,
messageText: '',
selectedParticipant: { participant_name: 'John Doe', ... },

// Methods
sendMessage() {
    // Your handler
}
```

### 2. Warning Modal
**File:** `warning-modal.blade.php`

**Usage:**
```blade
<x-examination-hub.modals.warning-modal />
```

**Data Requirements:**
```javascript
showWarningModal: false,
warningText: '',
selectedParticipant: { participant_name: 'John Doe', ... },

sendWarning() {
    // Your handler
}
```

**Styling:** Yellow accent with warning icon

### 3. Terminate Modal
**File:** `terminate-modal.blade.php`

**Usage:**
```blade
<x-examination-hub.modals.terminate-modal />
```

**Data Requirements:**
```javascript
showTerminateModal: false,
terminateReason: '', // Required
selectedParticipant: { participant_name: 'John Doe', ... },
actionLoading: false,

executeTerminate() {
    // Your handler
}
```

**Features:** Red accent, required reason field

### 4. Force Submit Modal
**File:** `force-submit-modal.blade.php`

**Usage:**
```blade
<x-examination-hub.modals.force-submit-modal />
```

**Data Requirements:**
```javascript
showForceSubmitModal: false,
forceSubmitReason: '', // Optional
selectedParticipant: { participant_name: 'John Doe', ... },
actionLoading: false,

executeForceSubmit() {
    // Your handler
}
```

**Features:** Orange accent, optional reason field

### 5. Extend Time Modal
**File:** `extend-time-modal.blade.php`

**Usage:**
```blade
<x-examination-hub.modals.extend-time-modal />
```

**Data Requirements:**
```javascript
showExtendTimeModal: false,
extendMinutes: 15, // Number 1-480
selectedParticipant: { participant_name: 'John Doe', ... },
actionLoading: false,

extendTime() {
    // Your handler
}
```

**Features:** Green accent, number input (1-480 minutes), sensible default 15 min

### 6. Readmission Modal
**File:** `readmission-modal.blade.php`

**Usage:**
```blade
<x-examination-hub.modals.readmission-modal />
```

**Data Requirements:**
```javascript
showReadmitModal: false,
readmitMode: 'continue', // 'continue' or 'fresh'
readmitExtraMinutes: 0, // 0-480
readmitReason: '', // Optional
selectedParticipant: { participant_name: 'John Doe', ... },
actionLoading: false,

grantReadmission() {
    // Your handler
}
```

**Features:** Purple accent, mode selector, extra time input, optional reason

## Implementation Example

### In Your Blade View
```blade
<div x-data="monitoringComponent()" class="p-6">
    <!-- Your main content here -->
    
    <!-- Include modals -->
    <x-examination-hub.modals.message-modal />
    <x-examination-hub.modals.warning-modal />
    <x-examination-hub.modals.terminate-modal />
    <x-examination-hub.modals.force-submit-modal />
    <x-examination-hub.modals.extend-time-modal />
    <x-examination-hub.modals.readmission-modal />
</div>

<script>
function monitoringComponent() {
    return {
        // Modal visibility states
        showMessageModal: false,
        showWarningModal: false,
        showTerminateModal: false,
        showForceSubmitModal: false,
        showExtendTimeModal: false,
        showReadmitModal: false,
        
        // Form fields
        messageText: '',
        warningText: '',
        terminateReason: '',
        forceSubmitReason: '',
        extendMinutes: 15,
        readmitMode: 'continue',
        readmitExtraMinutes: 0,
        readmitReason: '',
        
        // State
        selectedParticipant: null,
        actionLoading: false,
        
        // Modal openers
        openMessageModal(participant) {
            this.selectedParticipant = participant;
            this.messageText = '';
            this.showMessageModal = true;
        },
        
        openWarningModal(participant) {
            this.selectedParticipant = participant;
            this.warningText = '';
            this.showWarningModal = true;
        },
        
        // ... other openers ...
        
        // Action handlers
        async sendMessage() {
            if (!this.messageText.trim()) return;
            this.actionLoading = true;
            try {
                // Make API request
                await fetch(`/exams/${this.selectedParticipant.exam_id}/live-monitoring/message/${this.selectedParticipant.submission_id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ message: this.messageText }),
                });
                this.showMessageModal = false;
                // Show success toast
            } finally {
                this.actionLoading = false;
            }
        },
        
        // ... other handlers ...
    }
}
</script>
```

## Current Usage

These components are currently used in:
- ✅ `resources/views/examination-hub/live-monitoring/all-exams.blade.php` (Global exam monitoring)

Can be extended to:
- Per-exam live monitoring views (index.blade.php)
- Bulk admin action modals
- Other monitoring interfaces
- Dashboard components

## Component Architecture

Each modal:
1. Uses `x-teleport="body"` to render outside Alpine component
2. Conditional `x-show` for visibility
3. Smooth enter/leave transitions
4. Hardcoded Alpine.js bindings (no Blade parameters)
5. Consistent styling and structure
6. Color-coded by action type
7. Heroicons for visual consistency

## Key Features

✅ **Reusable** - Include in any view with one line: `<x-examination-hub.modals.message-modal />`
✅ **Consistent** - All modals follow the same design patterns
✅ **Dark Mode** - All modals support Tailwind dark: classes
✅ **Responsive** - Works on mobile, tablet, desktop
✅ **Accessible** - Semantic HTML, proper labels
✅ **Validated** - All Blade syntax checked and working
✅ **No Dependencies** - Only uses Alpine.js and Tailwind (already in app)

## Customization

To customize a modal for a different use case, copy the component and update:
- Modal title: Line 15 (or equivalent)
- State properties: `x-show`, `x-model`, `@click`
- Action handler: `@click="sendMessage()"`
- Styling colors: Tailwind classes
- Icons: Heroicons component

Example: Creating a custom notification modal:
```blade
<!-- Copy message-modal and modify -->
<x-show="showNotificationModal">
<!-- ... change title, styles, handler, etc ... -->
</x-show>
```

## Benefits

1. **DRY** - No modal duplication across multiple views
2. **Maintainability** - Update styling once, affects all uses
3. **Scalability** - Easy to create new modal types
4. **Organization** - All modals in one directory
5. **Clarity** - Each modal has a single, clear purpose
