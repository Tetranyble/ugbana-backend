<?php

namespace App\services;

class Segments
{
    public function education()
    {
        return [
            'education',
            'school',
            'qualification',
            'qualifications',
            'high school',
            'university',
            'academic',
            'background',
            'college',
        ];
    }

    public function degree()
    {
        return [
            'bachelor',
            "bachelor's",
            'masters',
            'master',
            "master's",
            'doctoral',
        ];
    }

    public function experience()
    {
        return [
            'employment history',
            'employment',
            'work history',
            'work experience',
            'professional experience',
            'professional background',
            'industry experience',
            'experience',
            'career history',
        ];
    }

    public function skill()
    {
        return [
            'credentials',
            'areas of experience',
            'areas of expertise',
            'areas of knowledge',
            'skills',
            'career related skills',
            'professional skills',
            'specialized skills',
            'technical skills',
            'computer skills',
            'computer knowledge',
            'technical experience',
            'proficiencies',
            'languages',
            'language competencies and skills',
            'programming languages',
        ];
    }

    public function project()
    {
        return [
            'academic projects',
            'personal projects',
            'other projects',
            'professional projects',
            'projects',
        ];
    }

    public function accomplishment()
    {
        return [
            'licenses',
            'presentations',
            'conference presentations',
            'conventions',
            'dissertations',
            'exhibits',
            'papers',
            'publications',
            'professional publications',
            'research grants',
            'research projects',
            'current research interests',
            'thesis',
            'theses',
            'activities and honors',
            'affiliations',
            'professional affiliations',
            'associations',
            'professional associations',
            'memberships',
            'professional memberships',
            'athletic involvement',
            'community involvement',
            'civic activities',
            'extra-Curricular activities',
            'professional activities',
            'volunteer work',
            'volunteer experience',
            'volunteering',
            'awards',
            'honors',
        ];
    }
}
