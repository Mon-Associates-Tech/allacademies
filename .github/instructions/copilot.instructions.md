---
applyTo: '**'
---
Provide project context and coding guidelines that AI should follow when generating code, answering questions, or reviewing changes.

- Use meaningful variable and function names.
- Follow the PSR-12 coding standard for PHP.
- Write unit tests for new features and bug fixes.
- Keep commits small and focused on a single issue.
- Include clear and descriptive commit messages.
- Update documentation and comments to reflect code changes.
- Use version control best practices (e.g., branching, pull requests).
- Ensure code is properly formatted and adheres to coding standards.
- Include error handling and validation for user inputs.
- Optimize performance and scalability of the code.
- Use tailwindcss for styling and layout.
- All views should be responsive and accessible.
- Use Alpine.js for interactivity and dynamic components.
- Livewire should be used for complex UI interactions and state management.
- Blade components should be used for reusable UI elements.
- auth layouts file is located at `resources/views/layouts/app.blade.php`
- when creating new views, use the the wrapper `<x-layouts.app>` for authenticated users and `<x-layouts.guest>` for guests.
- dark mode should be supported and implemented using Tailwind CSS classes.
- No need to create guide files after a task is completed.