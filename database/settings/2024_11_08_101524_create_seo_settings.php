<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('seo.meta_robots', 'index, follow');
        $this->migrator->add('seo.meta_googlebot', 'index, follow');
        $this->migrator->add('seo.meta_bingbot', 'index, follow');
        $this->migrator->add('seo.meta_title', [
            'en' => 'World For You',
            'fr' => 'Monde Pour Vous',
            'es' => 'Mundo Para Ti'
        ]);
        $this->migrator->add('seo.meta_description', [
            'en' => 'This is a description of the site.',
            'fr' => 'Ceci est une description du site.',
            'es' => 'Esta es una descripción del sitio.'
        ]);
        $this->migrator->add('seo.meta_keywords', [
            'en' => 'world, for, you',
            'fr' => 'monde, pour, vous',
            'es' => 'mundo, para, ti'
        ]);
        $this->migrator->add('seo.meta_author', [
            'en' => 'World For You',
            'fr' => 'Monde Pour Vous',
            'es' => 'Mundo Para Ti'
        ]);
        $this->migrator->add('seo.og_title', [
            'en' => 'World For You',
            'fr' => 'Monde Pour Vous',
            'es' => 'Mundo Para Ti'
        ]);
        $this->migrator->add('seo.og_description', [
            'en' => 'This is a description of the site.',
            'fr' => 'Ceci est une description du site.',
            'es' => 'Esta es una descripción del sitio.'
        ]);
        $this->migrator->add('seo.tw_title', [
            'en' => 'World For You',
            'fr' => 'Monde Pour Vous',
            'es' => 'Mundo Para Ti'
        ]);
        $this->migrator->add('seo.tw_description', [
            'en' => 'This is a description of the site.',
            'fr' => 'Ceci est une description du site.',
            'es' => 'Esta es una descripción del sitio.'
        ]);

    }
};
