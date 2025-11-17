<?php

class SkillModel {

    private $cacheKey = "skills";

    public function all()
    {
        if ($cache = CacheService::load($this->cacheKey)) return $cache;

        $rows = [];

        try{
            $pdo = DB::getInstance()->pdo();

            $stmt = $pdo->prepare("SELECT * FROM skills WHERE is_active = 1 ORDER BY id ASC");
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch (Throwable $e){
            app_log("SkillModel DB error: " . $e->getMessage(), "error");
            $rows = [];
        }

        // 3. Fallback defaults (never break page)
        if (empty($rows)) {
            $rows = $this->defaults();
        }

        CacheService::save($this->cacheKey, $rows);

        return $rows;
    }
    
    private function defaults(){
        return [
            [
                "skill_name"  => "HTML5",
                "icon_class"  => "fa-brands fa-html5",
                "color_class" => "text-orange-500"
            ],
            [
                "skill_name"  => "CSS3",
                "icon_class"  => "fa-brands fa-css3-alt",
                "color_class" => "text-blue-500"
            ],
            [
                "skill_name"  => "JavaScript",
                "icon_class"  => "fa-brands fa-js",
                "color_class" => "text-yellow-400"
            ],
            [
                "skill_name"  => "PHP",
                "icon_class"  => "fa-brands fa-php",
                "color_class" => "text-indigo-400"
            ],
            [
                "skill_name"  => "MySQL",
                "icon_class"  => "fa-solid fa-database",
                "color_class" => "text-teal-400"
            ],
            [
                "skill_name"  => "Tailwind CSS",
                "icon_class"  => "fa-solid fa-wind",
                "color_class" => "text-cyan-400"
            ],
            [
                "skill_name"  => "Git & GitHub",
                "icon_class"  => "fa-brands fa-git-alt",
                "color_class" => "text-orange-600"
            ],
        ];
    }
}
