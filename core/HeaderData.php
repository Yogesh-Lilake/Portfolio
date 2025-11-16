<?php
class HeaderData {

    private $pdo;

    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * Fetch header settings + navigation
     * Must NEVER break — must ALWAYS return fallback values.
     */
    public function get()
    {
        $header = [
            'site_title'   => SITE_TITLE,
            'logo_path'    => SITE_LOGO,
            'button_text'  => CTA_TEXT,
            'button_link'  => CTA_LINK,
            'accent_color' => ACCENT_COLOR,
        ];

        $nav_links = [
            ['label' => 'Home',    'url' => HOME_URL],
            ['label' => 'About',   'url' => ABOUT_URL],
            ['label' => 'Projects','url' => PROJECTS_URL],
            // ['label' => 'Notes',   'url' => NOTES_URL],
            ['label' => 'Contact', 'url' => CONTACT_URL]
        ];

        if (!$this->pdo) return ['header' => $header, 'nav' => $nav_links];

        try {
            $stmt = $this->pdo->query("SELECT * FROM header_settings LIMIT 1");
            if ($stmt && $row = $stmt->fetch()) {
                $header = array_merge($header, array_filter($row));
            }
        } catch (Exception $e) { /* ignore, use fallback */ }

        try {
            $nav = $this->pdo->query("SELECT label, url 
                                      FROM navigation_links 
                                      WHERE is_active = 1 
                                      ORDER BY order_no ASC");

            if ($nav) {
                $nav_links = $nav->fetchAll();
            }
        } catch (Exception $e) { /* ignore, use fallback */ }

        return [
            'header' => $header,
            'nav'    => $nav_links
        ];
    }
}
?>