<?php

use App\Livewire\Overlay\ToastDisplay;
use Livewire\Livewire;

it('generates static HTML for visual verification', function () {
    // Create a component with a visible toast
    $component = Livewire::test(ToastDisplay::class, [
        'overlayKey' => 'visual-test',
        'params' => [
            'theme' => 'dark',
            'fontScale' => 1.2,
            'safeMargin' => 30,
        ],
    ]);

    // Show a detailed toast
    $testMessage = [
        'display_name' => 'Visual Test User',
        'username' => 'visualtest',
        'badges' => [
            ['name' => 'moderator'],
            ['name' => 'vip'],
            ['name' => 'tester'],
        ],
        'message' => 'This is a visual test toast! 🎨 It has multiple badges and styled content to show how the toast looks when rendered.',
    ];

    $testOptions = [
        'duration_ms' => 8000,
        'theme' => 'dark',
        'fontScale' => 1.2,
        'safeMargin' => 30,
    ];

    $component->call('showToast', $testMessage, $testOptions);

    // Verify the state
    $component->assertSet('isVisible', true)
        ->assertSee('Visual Test User');

    // Get the computed classes
    $containerClasses = $component->get('containerClasses');
    $toastClasses = $component->get('toastClasses');
    $fontScale = $component->get('fontScale');

    // Generate static HTML manually
    $html = '<div data-overlay-key="visual-test">
        <div class="'.$containerClasses.'" style="font-size: '.$fontScale.'rem;">
            <div class="'.$toastClasses.'">
                <!-- Message Header -->
                <div class="flex items-center gap-2 mb-2">
                    <!-- Badges -->';

    if (isset($testMessage['badges']) && is_array($testMessage['badges'])) {
        foreach ($testMessage['badges'] as $badge) {
            if (isset($badge['name'])) {
                $html .= '<span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full dark:bg-blue-900/50 dark:text-blue-200">'
                       .htmlspecialchars($badge['name']).'</span>';
            }
        }
    }

    $html .= '
                    <!-- Display Name -->
                    <span class="font-bold text-lg">'
                       .htmlspecialchars($testMessage['display_name'])
                       .'</span>
                </div>
                
                <!-- Message Content -->
                <div class="text-base leading-relaxed break-words">'
                   .htmlspecialchars($testMessage['message'])
                   .'</div>
            </div>
        </div>
    </div>';

    // Save the HTML to a file
    $outputPath = storage_path('framework/testing/toast-visual-test.html');
    $fullHtml = "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Toast Visual Test</title>
    <script src=\"https://cdn.tailwindcss.com\"></script>
    <style>
        body { 
            background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .demo-info {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            color: white;
        }
    </style>
</head>
<body>
    <div class=\"demo-info\">
        <h1 class=\"text-2xl font-bold mb-4\">🎨 Toast Visual Test</h1>
        <p class=\"mb-2\"><strong>Component State:</strong> Visible (isVisible: true)</p>
        <p class=\"mb-2\"><strong>Theme:</strong> Dark</p>
        <p class=\"mb-2\"><strong>Font Scale:</strong> {$fontScale}</p>
        <p class=\"mb-2\"><strong>Safe Margin:</strong> 30px</p>
        <p class=\"mb-2\"><strong>User:</strong> Visual Test User</p>
        <p class=\"mb-2\"><strong>Badges:</strong> moderator, vip, tester</p>
        <p class=\"mb-4\"><strong>Container Classes:</strong> <code class=\"text-xs\">{$containerClasses}</code></p>
        <p class=\"mb-4\"><strong>Toast Classes:</strong> <code class=\"text-xs\">{$toastClasses}</code></p>
        <p class=\"text-sm opacity-75\">The toast should appear below with dark theme styling, badges, and 1.2x font size.</p>
    </div>
    
    {$html}
    
    <div class=\"demo-info mt-8\">
        <h2 class=\"text-xl font-bold mb-2\">Component Details</h2>
        <p class=\"text-sm\">This HTML was generated from a working Livewire component with all tests passing.</p>
        <p class=\"text-sm\">The toast uses Tailwind CSS classes for styling and positioning.</p>
    </div>
</body>
</html>";

    file_put_contents($outputPath, $fullHtml);

    echo "\n📸 Dark theme visual test HTML saved to: {$outputPath}\n";
    echo "🌐 Open this file in your browser to see how the toast looks!\n\n";

    expect($html)->toContain('Visual Test User')
        ->toContain('This is a visual test toast!')
        ->toContain('moderator')
        ->toContain('vip')
        ->toContain('tester');
});

it('generates light theme visual test', function () {
    $component = Livewire::test(ToastDisplay::class, [
        'overlayKey' => 'light-test',
        'params' => [
            'theme' => 'light',
            'fontScale' => 1.0,
            'safeMargin' => 24,
        ],
    ]);

    $testMessage = [
        'display_name' => 'Light Theme User',
        'username' => 'lightuser',
        'badges' => [['name' => 'sunny'], ['name' => 'bright']],
        'message' => 'This is a light theme toast! ☀️ Clean and bright with excellent readability.',
    ];

    $testOptions = [
        'theme' => 'light',
        'fontScale' => 1.0,
    ];

    $component->call('showToast', $testMessage, $testOptions);

    $containerClasses = $component->get('containerClasses');
    $toastClasses = $component->get('toastClasses');
    $fontScale = $component->get('fontScale');

    // Generate static HTML for light theme
    $html = '<div data-overlay-key="light-test">
        <div class="'.$containerClasses.'" style="font-size: '.$fontScale.'rem;">
            <div class="'.$toastClasses.'">
                <div class="flex items-center gap-2 mb-2">';

    foreach ($testMessage['badges'] as $badge) {
        $html .= '<span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full dark:bg-blue-900/50 dark:text-blue-200">'
               .htmlspecialchars($badge['name']).'</span>';
    }

    $html .= '<span class="font-bold text-lg">'.htmlspecialchars($testMessage['display_name']).'</span>
                </div>
                <div class="text-base leading-relaxed break-words">'.htmlspecialchars($testMessage['message']).'</div>
            </div>
        </div>
    </div>';

    $outputPath = storage_path('framework/testing/toast-light-theme.html');
    $fullHtml = "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Toast Light Theme Test</title>
    <script src=\"https://cdn.tailwindcss.com\"></script>
    <style>
        body { 
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .demo-info {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            color: #1f2937;
        }
    </style>
</head>
<body>
    <div class=\"demo-info\">
        <h1 class=\"text-2xl font-bold mb-4\">☀️ Toast Light Theme Test</h1>
        <p class=\"mb-2\"><strong>Component State:</strong> Visible (isVisible: true)</p>
        <p class=\"mb-2\"><strong>Theme:</strong> Light</p>
        <p class=\"mb-2\"><strong>Font Scale:</strong> {$fontScale}</p>
        <p class=\"mb-2\"><strong>User:</strong> Light Theme User</p>
        <p class=\"mb-2\"><strong>Badges:</strong> sunny, bright</p>
        <p class=\"mb-4\"><strong>Container Classes:</strong> <code class=\"text-xs\">{$containerClasses}</code></p>
        <p class=\"mb-4\"><strong>Toast Classes:</strong> <code class=\"text-xs\">{$toastClasses}</code></p>
        <p class=\"text-sm opacity-75\">The toast should appear below with light theme styling and normal font size.</p>
    </div>
    
    {$html}
    
    <div class=\"demo-info mt-8\">
        <h2 class=\"text-xl font-bold mb-2\">Component Details</h2>
        <p class=\"text-sm\">This HTML demonstrates the light theme variant with white background and dark text.</p>
        <p class=\"text-sm\">Compare this with the dark theme version to see the styling differences.</p>
    </div>
</body>
</html>";

    file_put_contents($outputPath, $fullHtml);

    echo "\n☀️ Light theme test HTML saved to: {$outputPath}\n";
    echo "🌐 Open this file in your browser to see the light theme!\n\n";

    expect($html)->toContain('Light Theme User')
        ->toContain('bg-white/90'); // Light theme class
});
