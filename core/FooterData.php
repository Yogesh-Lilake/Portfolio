<?php

class FooterData {

    private $pdo;

    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function get()
    {
        // Default fallback values
        $footer = [
            'brand_name' => 'Yogesh Lilake',
            'footer_description' => 'Full Stack & Android Developer — Crafting digital solutions.',
            'developer_name' => 'Yogesh Lilake',
            'accent_color' => '#ff5a5a'
        ];

        $footer_links = [
            ['label' => 'Home', 'url' => HOME_URL],
            ['label' => 'About', 'url' => ABOUT_URL],
            ['label' => 'Projects', 'url' => PROJECTS_URL],
            ['label' => 'Contact', 'url' => CONTACT_URL],
        ];

        $social_links = [
            ['platform' => 'GitHub', 'url' => 'https://github.com', 'icon_class' => 'fa-github'],
            ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com', 'icon_class' => 'fa-linkedin'],
            ['platform' => 'Twitter', 'url' => 'https://twitter.com', 'icon_class' => 'fa-twitter'],
            ['platform' => 'Email', 'url' => 'mailto:hello@example.com', 'icon_class' => 'fa-envelope'],
        ];

        if (!$this->pdo) {
            return [
                'footer' => $footer,
                'links' => $footer_links,
                'social' => $social_links
            ];
        }

        // Fetch footer settings
        try {
            $stmt = $this->pdo->query("SELECT * FROM footer_settings LIMIT 1");
            if ($stmt && $data = $stmt->fetch()) {
                $footer = array_merge($footer, array_filter($data));
            }
        } catch (Exception $e) {/* ignore */}

        // Quick links
        try {
            $stmt = $this->pdo->query("SELECT label, url FROM navigation_links WHERE is_active = 1 ORDER BY order_no ASC");
            if ($stmt) $footer_links = $stmt->fetchAll();
        } catch (Exception $e) {/* ignore */}

        // Socials
        try {
            $stmt = $this->pdo->query("SELECT platform, url, icon_class FROM social_links WHERE is_active = 1");
            if ($stmt) $social_links = $stmt->fetchAll();
        } catch (Exception $e) {/* ignore */}

        return [
            'footer' => $footer,
            'links' => $footer_links,
            'social' => $social_links
        ];
    }
}
?>
