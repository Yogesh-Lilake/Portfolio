<?php

class FooterData {

    private $pdo;

    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function get()
    {
        // --- FALLBACK DEFAULT VALUES ---
        $footer = [
            'brand_name'        => SITE_TITLE,
            'footer_description'=> 'Full Stack & Android Developer — Crafting digital solutions.',
            'developer_name'    => SITE_TITLE,
            'accent_color'      => ACCENT_COLOR
        ];

        // --- FALLBACK QUICK LINKS ---
        $footer_links = [
            ['label' => 'Home',     'url' => HOME_URL],
            ['label' => 'About',    'url' => ABOUT_URL],
            ['label' => 'Projects', 'url' => PROJECTS_URL],
            ['label' => 'Contact',  'url' => CONTACT_URL],
        ];

        // --- FALLBACK SOCIAL LINKS ---
        $social_links = [
            ['platform' => 'GitHub', 'url' => 'https://github.com', 'icon_class' => 'fa-github'],
            ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com', 'icon_class' => 'fa-linkedin'],
            ['platform' => 'Twitter', 'url' => 'https://twitter.com', 'icon_class' => 'fa-twitter'],
            ['platform' => 'Email', 'url' => 'mailto:hello@example.com', 'icon_class' => 'fa-envelope'],
        ];

        // If no database connection → return fallback
        if (!$this->pdo) {
            return [
                'footer' => $footer,
                'links'  => $footer_links,
                'social' => $social_links
            ];
        }

        // --- DB: FOOTER SETTINGS ---
        try {
            $stmt = $this->pdo->query("SELECT * FROM footer_settings LIMIT 1");
            if ($stmt && $row = $stmt->fetch()) {
                $footer = array_merge($footer, array_filter($row));
            }
        } catch (Exception $e) {/* ignore */}

        // --- DB: QUICK LINKS ---
        try {
            $stmt = $this->pdo->query("
                SELECT label, url 
                FROM navigation_links 
                WHERE is_active = 1 
                ORDER BY order_no ASC
            ");
            if ($stmt) {
                $data = $stmt->fetchAll();
                if (!empty($data)) {
                    $footer_links = $data;
                }
            }
        } catch (Exception $e) {}

        // --- DB: SOCIAL LINKS ---
        try {
            $stmt = $this->pdo->query("
                SELECT platform, url, icon_class 
                FROM social_links 
                WHERE is_active = 1
            ");
            if ($stmt) {
                $data = $stmt->fetchAll();
                if (!empty($data)) {
                    $social_links = $data;
                }
            }
        } catch (Exception $e) {}

        return [
            'footer' => $footer,
            'links'  => $footer_links,
            'social' => $social_links
        ];
    }
}
?>
