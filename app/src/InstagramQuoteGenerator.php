<?php

namespace App;

/**
 * Instagram Quote Image Generator
 * Creates 1080x1350px images with beautiful gradients and quotes
 */
class InstagramQuoteGenerator
{
    private const WIDTH = 1080;
    private const HEIGHT = 1350;
    
    // Beautiful gradient color combinations
    private const GRADIENTS = [
        ['#667eea', '#764ba2'], // Purple Dream
        ['#f093fb', '#f5576c'], // Pink Sunset
        ['#4facfe', '#00f2fe'], // Ocean Blue
        ['#43e97b', '#38f9d7'], // Mint Green
        ['#fa709a', '#fee140'], // Sunset Orange
        ['#a8edea', '#fed6e3'], // Cotton Candy
        ['#ffecd2', '#fcb69f'], // Peach
        ['#ff8a80', '#ff5722'], // Coral
        ['#667db6', '#0082c8'], // Deep Ocean
        ['#f8ffae', '#43c6ac'], // Lime Fresh
        ['#fdbb2d', '#22c1c3'], // Golden Teal
        ['#ee9ca7', '#ffdde1'], // Rose Gold
        ['#2196f3', '#21cbf3'], // Sky Blue
        ['#4776e6', '#8e54e9'], // Purple Blue
        ['#ffeaa7', '#fab1a0']  // Warm Sunset
    ];
    
    // Inspirational quotes
    private const QUOTES = [
        "The only way to do great work is to love what you do.",
        "Innovation distinguishes between a leader and a follower.",
        "Stay hungry, stay foolish.",
        "Your limitation—it's only your imagination.",
        "Great things never come from comfort zones.",
        "Dream it. Wish it. Do it.",
        "Success doesn't just find you. You have to go out and get it.",
        "The harder you work for something, the greater you'll feel when you achieve it.",
        "Dream bigger. Do bigger.",
        "Don't stop when you're tired. Stop when you're done.",
        "Wake up with determination. Go to bed with satisfaction.",
        "Do something today that your future self will thank you for.",
        "Little things make big days.",
        "It's going to be hard, but hard does not mean impossible.",
        "Don't wait for opportunity. Create it."
    ];

    /**
     * Add border to the image
     */
    private function addBorder($image, array $options): void
    {
        if ($options['border_width'] <= 0) return;
        
        $borderWidth = $options['border_width'];
        $alpha = (int)((1 - $options['border_opacity']) * 127);
        
        // Create border color with alpha
        $borderColor = imagecolorallocatealpha(
            $image, 
            $options['border_color'][0], 
            $options['border_color'][1], 
            $options['border_color'][2], 
            $alpha
        );
        
        // Draw border rectangles
        for ($i = 0; $i < $borderWidth; $i++) {
            // Top border
            imageline($image, $i, $i, self::WIDTH - $i - 1, $i, $borderColor);
            // Bottom border
            imageline($image, $i, self::HEIGHT - $i - 1, self::WIDTH - $i - 1, self::HEIGHT - $i - 1, $borderColor);
            // Left border
            imageline($image, $i, $i, $i, self::HEIGHT - $i - 1, $borderColor);
            // Right border
            imageline($image, self::WIDTH - $i - 1, $i, self::WIDTH - $i - 1, self::HEIGHT - $i - 1, $borderColor);
        }
    }
    
    /**
     * Create an Instagram quote image
     */
    public function createQuoteImage(
        ?string $quote = null, 
        ?array $gradientColors = null, 
        string $outputPath = null, 
        array $options = []
    ): string {
        
        // Set default options
        $options = array_merge([
            'font_size' => 172, // Much bigger font size
            'font_color' => [255, 255, 255],
            'font_path' => null, // Path to TTF font file
            'text_shadow' => true,
            'margin' => 120,
            'line_height' => 1.5,
            'quote_marks' => true,
            'author' => null,
            'gradient_direction' => 'diagonal', // 'horizontal', 'vertical', 'diagonal', 'radial'
            'border_width' => 20, // Border width in pixels
            'border_color' => [255, 255, 255], // White border by default
            'border_opacity' => 0.3 // Border opacity (0-1)
        ], $options);
        
        // Create image
        $image = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        
        // Get random quote if not provided
        if (!$quote) {
            $quote = self::QUOTES[array_rand(self::QUOTES)];
        }
        
        // Get random gradient if not provided
        if (!$gradientColors) {
            $gradientColors = self::GRADIENTS[array_rand(self::GRADIENTS)];
        }
        
        // Create gradient background
        $this->createGradient($image, $gradientColors, $options['gradient_direction']);
        
        // Add border
        $this->addBorder($image, $options);
        
        // Add quote text
        $this->addQuoteText($image, $quote, $options);
        
        // Add author if provided
        if ($options['author']) {
            $this->addAuthor($image, $options['author'], $options);
        }
        
        // Generate output path if not provided
        if (!$outputPath) {
            $outputPath = 'instagram_quote_' . time() . '.png';
        }
        
        // Save image
        imagepng($image, $outputPath, 9);
        imagedestroy($image);
        
        return $outputPath;
    }
    
    /**
     * Create gradient background
     */
    private function createGradient($image, array $colors, string $direction): void
    {
        $color1 = $this->hexToRgb($colors[0]);
        $color2 = $this->hexToRgb($colors[1]);
        
        switch ($direction) {
            case 'horizontal':
                $this->createHorizontalGradient($image, $color1, $color2);
                break;
            case 'vertical':
                $this->createVerticalGradient($image, $color1, $color2);
                break;
            case 'radial':
                $this->createRadialGradient($image, $color1, $color2);
                break;
            case 'diagonal':
            default:
                $this->createDiagonalGradient($image, $color1, $color2);
                break;
        }
    }
    
    /**
     * Create diagonal gradient
     */
    private function createDiagonalGradient($image, array $color1, array $color2): void
    {
        for ($x = 0; $x < self::WIDTH; $x++) {
            for ($y = 0; $y < self::HEIGHT; $y++) {
                // Calculate progress based on diagonal distance
                $progress = ($x + $y) / (self::WIDTH + self::HEIGHT);
                
                $r = (int)($color1[0] + ($color2[0] - $color1[0]) * $progress);
                $g = (int)($color1[1] + ($color2[1] - $color1[1]) * $progress);
                $b = (int)($color1[2] + ($color2[2] - $color1[2]) * $progress);
                
                $color = imagecolorallocate($image, $r, $g, $b);
                imagesetpixel($image, $x, $y, $color);
            }
        }
    }
    
    /**
     * Create horizontal gradient
     */
    private function createHorizontalGradient($image, array $color1, array $color2): void
    {
        for ($x = 0; $x < self::WIDTH; $x++) {
            $progress = $x / self::WIDTH;
            
            $r = (int)($color1[0] + ($color2[0] - $color1[0]) * $progress);
            $g = (int)($color1[1] + ($color2[1] - $color1[1]) * $progress);
            $b = (int)($color1[2] + ($color2[2] - $color1[2]) * $progress);
            
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, $x, 0, $x, self::HEIGHT, $color);
        }
    }
    
    /**
     * Create vertical gradient
     */
    private function createVerticalGradient($image, array $color1, array $color2): void
    {
        for ($y = 0; $y < self::HEIGHT; $y++) {
            $progress = $y / self::HEIGHT;
            
            $r = (int)($color1[0] + ($color2[0] - $color1[0]) * $progress);
            $g = (int)($color1[1] + ($color2[1] - $color1[1]) * $progress);
            $b = (int)($color1[2] + ($color2[2] - $color1[2]) * $progress);
            
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $y, self::WIDTH, $y, $color);
        }
    }
    
    /**
     * Create radial gradient
     */
    private function createRadialGradient($image, array $color1, array $color2): void
    {
        $centerX = self::WIDTH / 2;
        $centerY = self::HEIGHT / 2;
        $maxDistance = sqrt($centerX * $centerX + $centerY * $centerY);
        
        for ($x = 0; $x < self::WIDTH; $x++) {
            for ($y = 0; $y < self::HEIGHT; $y++) {
                $distance = sqrt(($x - $centerX) ** 2 + ($y - $centerY) ** 2);
                $progress = min($distance / $maxDistance, 1);
                
                $r = (int)($color1[0] + ($color2[0] - $color1[0]) * $progress);
                $g = (int)($color1[1] + ($color2[1] - $color1[1]) * $progress);
                $b = (int)($color1[2] + ($color2[2] - $color1[2]) * $progress);
                
                $color = imagecolorallocate($image, $r, $g, $b);
                imagesetpixel($image, $x, $y, $color);
            }
        }
    }
    
    /**
     * Add quote text to image with perfect vertical and horizontal alignment
     */
    private function addQuoteText($image, string $quote, array $options): void
    {
        // Add quote marks if enabled
        if ($options['quote_marks']) {
            $quote = '"' . $quote . '"';
        }
        
        // Account for border in margins
        $effectiveMargin = $options['margin'] + $options['border_width'];
        $maxWidth = self::WIDTH - (2 * $effectiveMargin);
        
        // Wrap text with the effective width
        $wrappedText = $this->wrapTextToWidth($quote, $options['font_size'], $options['font_path'], $maxWidth);
        
        // Get text dimensions
        $textDimensions = $this->getTextDimensions($wrappedText, $options['font_size'], $options['font_path'], $options['line_height']);
        
        // Perfect center alignment
        $x = (self::WIDTH - $textDimensions['width']) / 2;
        $y = (self::HEIGHT - $textDimensions['height']) / 2;
        
        // Create colors
        $textColor = imagecolorallocate($image, ...$options['font_color']);
        
        // Add text shadow for better readability
        if ($options['text_shadow']) {
            $shadowColor = imagecolorallocatealpha($image, 0, 0, 0, 50); // Semi-transparent black
            $this->drawCenteredMultilineText($image, $wrappedText, $x + 3, $y + 3, $shadowColor, $options);
        }
        
        // Draw main text
        $this->drawCenteredMultilineText($image, $wrappedText, $x, $y, $textColor, $options);
    }
    
    /**
     * Add author text
     */
    private function addAuthor($image, string $author, array $options): void
    {
        $authorText = "— " . $author;
        $fontSize = $options['font_size'] * 0.5; // Smaller than main quote
        $effectiveMargin = $options['margin'] + $options['border_width'];
        
        $textColor = imagecolorallocate($image, ...$options['font_color']);
        
        if ($options['font_path'] && file_exists($options['font_path'])) {
            $bbox = imagettfbbox($fontSize, 0, $options['font_path'], $authorText);
            $width = $bbox[4] - $bbox[0];
            $x = (self::WIDTH - $width) / 2;
            $y = self::HEIGHT - $effectiveMargin - 20; // Position above bottom margin
            
            imagettftext($image, $fontSize, 0, $x, $y, $textColor, $options['font_path'], $authorText);
        } else {
            $x = (self::WIDTH - strlen($authorText) * imagefontwidth(4)) / 2;
            $y = self::HEIGHT - $effectiveMargin - 30;
            imagestring($image, 4, $x, $y, $authorText, $textColor);
        }
    }
    
    /**
     * Wrap text to specific width
     */
    private function wrapTextToWidth(string $text, int $fontSize, ?string $fontPath, int $maxWidth): string
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';
        
        foreach ($words as $word) {
            $testLine = $currentLine . ($currentLine ? ' ' : '') . $word;
            $testWidth = $this->getTextWidth($testLine, $fontSize, $fontPath);
            
            if ($testWidth <= $maxWidth) {
                $currentLine = $testLine;
            } else {
                if ($currentLine) {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;
                
                // Handle very long single words
                while ($this->getTextWidth($currentLine, $fontSize, $fontPath) > $maxWidth && strlen($currentLine) > 1) {
                    $lines[] = substr($currentLine, 0, -1);
                    $currentLine = substr($currentLine, -1);
                }
            }
        }
        
        if ($currentLine) {
            $lines[] = $currentLine;
        }
        
        return implode("\n", $lines);
    }
    
    /**
     * Get text dimensions including line height
     */
    private function getTextDimensions(string $text, int $fontSize, ?string $fontPath, float $lineHeight): array
    {
        $lines = explode("\n", $text);
        $maxWidth = 0;
        $totalHeight = 0;
        
        foreach ($lines as $i => $line) {
            $width = $this->getTextWidth($line, $fontSize, $fontPath);
            $maxWidth = max($maxWidth, $width);
            
            if ($i === 0) {
                $totalHeight += $fontSize; // First line
            } else {
                $totalHeight += $fontSize * $lineHeight; // Subsequent lines with line height
            }
        }
        
        return ['width' => $maxWidth, 'height' => $totalHeight];
    }
    
    /**
     * Draw multiline text with perfect centering
     */
    private function drawCenteredMultilineText($image, string $text, int $baseX, int $baseY, $color, array $options): void
    {
        $lines = explode("\n", $text);
        $lineHeight = $options['font_size'] * $options['line_height'];
        
        // Calculate starting Y position for vertical centering
        $totalTextHeight = count($lines) * $options['font_size'] + (count($lines) - 1) * ($lineHeight - $options['font_size']);
        $startY = $baseY + ($options['font_size'] * 0.8); // Adjust baseline
        
        foreach ($lines as $i => $line) {
            $lineY = $startY + ($i * $lineHeight);
            
            if ($options['font_path'] && file_exists($options['font_path'])) {
                // Get line width for horizontal centering
                $bbox = imagettfbbox($options['font_size'], 0, $options['font_path'], $line);
                $lineWidth = $bbox[4] - $bbox[0];
                $lineX = (self::WIDTH - $lineWidth) / 2;
                
                imagettftext($image, $options['font_size'], 0, $lineX, $lineY, $color, $options['font_path'], $line);
            } else {
                // Center each line with built-in font
                $lineX = (self::WIDTH - strlen($line) * imagefontwidth(5)) / 2;
                $adjustedY = $lineY - ($options['font_size'] * 0.8); // Adjust for built-in font
                imagestring($image, 5, $lineX, $adjustedY, $line, $color);
            }
        }
    }
    
    /**
     * Wrap text to fit within margins
     */
    private function wrapText(string $text, int $fontSize, ?string $fontPath): string
    {
        return $this->wrapTextToWidth($text, $fontSize, $fontPath, self::WIDTH - 240); // Fallback method
    }
    
    /**
     * Get text width
     */
    private function getTextWidth(string $text, int $fontSize, ?string $fontPath): int
    {
        if ($fontPath && file_exists($fontPath)) {
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
            return $bbox[4] - $bbox[0];
        } else {
            return strlen($text) * imagefontwidth(5);
        }
    }
    
    /**
     * Get text bounding box
     */
    private function getTextBoundingBox(string $text, int $fontSize, ?string $fontPath): array
    {
        return $this->getTextDimensions($text, $fontSize, $fontPath, 1.4); // Fallback method
    }
    
    /**
     * Draw multiline text
     */
    private function drawMultilineText($image, string $text, int $x, int $y, $color, array $options): void
    {
        // Use the new centered method
        $this->drawCenteredMultilineText($image, $text, $x, $y, $color, $options);
    }
    
    /**
     * Convert hex color to RGB array
     */
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ];
    }
    
    /**
     * Get a random quote
     */
    public function getRandomQuote(): string
    {
        return self::QUOTES[array_rand(self::QUOTES)];
    }
    
    /**
     * Get random gradient colors
     */
    public function getRandomGradient(): array
    {
        return self::GRADIENTS[array_rand(self::GRADIENTS)];
    }
}

// Usage Examples
if (basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    $generator = new InstagramQuoteGenerator();
    
    // Example 1: Basic usage with random quote and gradient
    $imagePath1 = $generator->createQuoteImage();
    echo "Created image: $imagePath1\n";
    
    // Example 2: Custom quote with specific gradient
    $imagePath2 = $generator->createQuoteImage(
        "Success is not final, failure is not fatal: it is the courage to continue that counts.",
        ['#667eea', '#764ba2'],
        'custom_quote.png'
    );
    echo "Created image: $imagePath2\n";
    
    // Example 3: Full customization with bigger text and border
    $imagePath3 = $generator->createQuoteImage(
        "The future belongs to those who believe in the beauty of their dreams.",
        ['#ff8a80', '#ff5722'],
        'dream_quote.png',
        [
            'font_size' => 80,          // Even bigger font
            'font_color' => [255, 255, 255],
            'gradient_direction' => 'radial',
            'author' => 'Eleanor Roosevelt',
            'quote_marks' => true,
            'border_width' => 25,       // Nice border
            'border_color' => [255, 255, 255],
            'border_opacity' => 0.4
        ]
    );
    echo "Created image: $imagePath3\n";
}