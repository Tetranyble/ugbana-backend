<?php

namespace App\services;

use App\Models\Degree;
use App\Models\Employer;
use App\Models\Position;
use App\Models\Skill;
use App\Models\University;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Intervention\Image\Image;
use NlpTools\Tokenizers\WhitespaceAndPunctuationTokenizer;
use NlpTools\Tokenizers\WhitespaceTokenizer;
use Spatie\PdfToText\Pdf;
use Web64\LaravelNlp\Facades\NLP;

class Parser
{
    const MIN_NAME_LENGTH = 6;

    protected Segments $segments;

    protected Helper $helper;

    protected string $pdftotextBinaryPath;

    public function __construct(string $binaryPath = '/usr/local/bin/pdftotext')
    {
        $this->segments = new Segments();
        $this->helper = new Helper();
        $this->pdftotextBinaryPath = env('PATH_PDFTOTEXT') ?? $binaryPath;
    }

    public function getData($pdf)
    {

        $text = (new Pdf($this->pdftotextBinaryPath))
            ->setPdf($pdf)->text();

        $textLayout = (new Pdf(env('PATH_PDFTOTEXT')))
            ->setOptions(['layout', 'r 96'])
            ->setPdf($pdf)->text();

        return [
            'fullname' => $this->getName($text),
            'email' => $this->getEmail($text),
            'phone' => $this->getPhone($text),
            'nationality' => $this->getNationality($text),
            'birthday' => $this->getBirthday($text),
            'gender' => $this->getGender($text),
            'linkedin' => $this->getLinkedInProfile($text),
            'github' => $this->getGithubProfile($text),
            'skills' => $this->getSkills($text),
            'languages' => $this->getLanguages($text),
            //'image'       => $this->getProfilePicture($pdf, $hash),

            'education' => $this->parseEducationSegment($textLayout),
            'experience' => $this->parseExperienceSegment($textLayout),
        ];

    }

    public function emptyDirs()
    {

        $dirs = ['cv', 'images', 'tmp'];

        foreach ($dirs as $dir) {

            $dir = storage_path().'/app/public/'.$dir;

            $files = glob($dir.'/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
    }

    public function getLines($text)
    {

        return array_values(array_filter(explode("\n", $text)));
    }

    public function getTokens($text, $type = 'whitespace')
    {

        if ($type == 'whitespaceAndPunctuation') {

            $tok = new WhitespaceAndPunctuationTokenizer();

        } else {

            $tok = new WhitespaceTokenizer();
        }

        $tokens = [];

        $lines = $this->getLines($text);

        foreach ($lines as $line) {

            $lineTokens = $tok->tokenize($line);

            foreach ($lineTokens as $token) {
                $tokens[] = $token;
            }
        }

        return $tokens;
    }

    public function getText($text)
    {

        return implode(' ', $this->getTokens($text));
    }

    public function nGrams($text, $n = 3)
    {

        $tokens = $this->getTokens($text, 'whitespaceAndPunctuation');

        $len = count($tokens);
        $ngram = [];

        for ($i = 0; $i + $n <= $len; $i++) {
            $string = '';
            for ($j = 0; $j < $n; $j++) {
                $string .= ' '.$tokens[$j + $i];
            }
            $ngram[$i] = $string;
        }

        return $ngram;

    }

    public function getName($text, $names = ['name'])
    {

        $userSegment = $this->getUserSegment($text);

        $tok = new WhitespaceAndPunctuationTokenizer();

        foreach ($userSegment as $line) {

            $lineTokens = $tok->tokenize($line);

            foreach ($lineTokens as $token) {
                if (strlen($token) > 2) {
                    if (in_array(ucfirst(strtolower($token)), $names)) {
                        if (mb_strlen($line) > self::MIN_NAME_LENGTH) {
                            return $this->normalizeName($line);
                        }
                    }
                }
            }
        }

        foreach ($userSegment as $line) {

            $entities = NLP::spacy_entities($line, 'en');

            if (! empty($entities)) {
                if (isset($entities['PERSON'])) {
                    if (mb_strlen($line) > self::MIN_NAME_LENGTH) {
                        return $this->normalizeName($line);
                    }
                }
            }
        }

        return null;
    }

    public function getNationality($text, $nationalities = ['nationalities'])
    {

        $userSegment = $this->getUserSegment($text);

        $tok = new WhitespaceAndPunctuationTokenizer();

        foreach ($userSegment as $line) {

            $lineTokens = $tok->tokenize($line);

            foreach ($lineTokens as $token) {
                if (strlen($token) > 3) {
                    if (in_array(ucfirst(strtolower($token)), $nationalities)) {
                        return $token;
                    }
                }
            }
        }

        return null;
    }

    public function getBirthday($text)
    {

        $pattern = '/([0-9]{2})\/([0-9]{2})\/([0-9]{4})|([0-9]{2})\.([0-9]{2})\.([0-9]{4})/i';

        $userSegment = $this->getUserSegment($text);

        //dd($userSegment);

        foreach ($userSegment as $line) {

            preg_match_all($pattern, $line, $matches);

            if (count($matches) > 0) {

                if (isset($matches[0][0])) {
                    return $this->normalizeBirthDay($matches[0][0]);
                }
            }
        }

        return null;
    }

    public function getGender($text)
    {

        $tok = new WhitespaceAndPunctuationTokenizer();

        $userSegment = $this->getUserSegment($text);

        foreach ($userSegment as $line) {

            $lineTokens = $tok->tokenize($line);

            foreach ($lineTokens as $token) {
                if (in_array(strtolower($token), ['male', 'female'])) {
                    return ucfirst($token);
                }
            }
        }

        return null;
    }

    public function getEmail($text)
    {

        $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';

        preg_match_all($pattern, $text, $matches);

        if (count($matches) > 0) {

            if (isset($matches[0][0])) {
                return $matches[0][0];
            }
        }

        return null;
    }

    public function getPhone($text)
    {

        $pattern = "/\d{9,}/i";

        $text = str_replace([' ', '-', '(', ')', '/'], ['', '', '', '', ''], $text);

        preg_match_all($pattern, $text, $matches);

        if (count($matches) > 0) {
            if (isset($matches[0][0])) {
                return $matches[0][0];
            }
        }

        return null;
    }

    public function getProfilePicture($pdf, $hash)
    {

        $tmp = storage_path().'/app/public/tmp';

        $cmd = env('PATH_PDFIMAGES').' -all -f 1 '.$pdf.' '.$tmp.'/prefix';
        exec($cmd);

        $images = array_diff(preg_grep('~\.(jpeg|jpg|png)$~', scandir($tmp)), ['.', '..', '.DS_Store']);
        $images = array_slice($images, 0, 3, true);

        foreach ($images as $image) {

            $imageInfo = getimagesize($tmp.'/'.$image);

            $width = $imageInfo[0];
            $height = $imageInfo[1];

            if ($height > 50) {

                if ($width > 200) {

                    $img = Image::make($tmp.'/'.$image);
                    $img->resize(200, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $img->save($tmp.'/'.$image);
                }

                $ext = File::extension($tmp.'/'.$image);

                if ($ext == 'png' || $ext == 'jpeg') {
                    $newImage = str_replace('png', 'jpg', $image);
                    $newImage = str_replace('jpeg', 'jpg', $newImage);

                    $img = Image::make($tmp.'/'.$image)->encode('jpg', 75);
                    $img->save($tmp.'/'.$newImage);
                    $image = $newImage;
                }

                $isFace = FaceDetect::extract($tmp.'/'.$image)->face_found;

                if ($isFace) {
                    $imageDir = storage_path().'/app/public/images/'.$hash.'.jpg';
                    FaceDetect::extract($tmp.'/'.$image)->save($imageDir);
                    break;
                }
            }
        }

        return (isset($imageDir)) ? $hash.'.jpg' : null;
    }

    public function getSkills($text)
    {

        $allSkills = []; //Skill::getSkills();

        $skills = [];

        $text = $this->getText($text);

        foreach ($allSkills as $skill) {

            if ($this->helper->isWordInText($skill, $text)) {

                $skills[] = $skill;
            }
        }

        return $skills;
    }

    public function getLanguages($text)
    {

        $allLanguages = []; //Skill::getLanguages();

        $languages = [];

        $text = $this->getText($text);

        foreach ($allLanguages as $language) {

            if ($this->helper->isWordInText($language, $text)) {

                $languages[] = $language;
            }
        }

        return $languages;
    }

    public function getLinkedInProfile($text)
    {

        $needle = 'linkedin.com';

        $tokens = $this->getTokens($text);

        foreach ($tokens as $token) {

            $pos = strpos(strtolower($token), $needle);

            if ($pos > -1) {
                return $token;
            }
        }

        return '';
    }

    public function getGithubProfile($text)
    {

        $needle = 'github.com';

        $tokens = $this->getTokens($text);

        foreach ($tokens as $token) {

            $pos = strpos(strtolower($token), $needle);

            if ($pos > -1) {
                return $token;
            }
        }

        return '';
    }

    /* SEGMENTS */

    public function getEducationSegmentKeywords()
    {

        return $this->segments->education();

    }

    public function getDegreeSegmentKeywords()
    {

        return $this->segments->degree();

    }

    public function getExperienceSegmentKeywords()
    {

        return $this->segments->experience();

    }

    public function getSkillSegmentKeywords()
    {

        return $this->segments->skill();
    }

    public function getProjectSegmentKeywords()
    {

        return $this->segments->project();
    }

    public function getAccomplishmentSegmentKeywords()
    {

        return $this->segments->accomplishment();

    }

    public function searchKeywordsInText($keywords, $text)
    {

        foreach ($keywords as $keyword) {
            if ($this->helper->isWordInText($keyword, $text)) {
                return true;
            }
        }

        return false;
    }

    public function getUserSegment($text)
    {

        $segment = [];

        $lines = $this->getLines($text);

        $educationKeywords = $this->getEducationSegmentKeywords();
        $degreeKeywords = $this->getDegreeSegmentKeywords();
        $projectKeywords = $this->getProjectSegmentKeywords();
        $skillKeywords = $this->getSkillSegmentKeywords();
        $accomplishmentKeywords = $this->getAccomplishmentSegmentKeywords();
        $experienceKeywords = $this->getExperienceSegmentKeywords();

        foreach ($lines as $line) {

            if (! $this->searchKeywordsInText($educationKeywords, $line) &&
                ! $this->searchKeywordsInText($degreeKeywords, $line) &&
                ! $this->searchKeywordsInText($projectKeywords, $line) &&
                ! $this->searchKeywordsInText($skillKeywords, $line) &&
                ! $this->searchKeywordsInText($accomplishmentKeywords, $line) &&
                ! $this->searchKeywordsInText($experienceKeywords, $line)
            ) {
                $segment[] = $line;
            } else {
                break;
            }
        }

        return $segment;
    }

    public function getEducationSegment($text)
    {

        $segment = [];

        $lines = $this->getLines($text);

        $educationKeywords = $this->getEducationSegmentKeywords();
        $projectKeywords = $this->getProjectSegmentKeywords();
        $skillKeywords = $this->getSkillSegmentKeywords();
        $accomplishmentKeywords = $this->getAccomplishmentSegmentKeywords();
        $experienceKeywords = $this->getExperienceSegmentKeywords();

        $i = 0;
        foreach ($lines as $line) {

            $i++;
            $flag = false;

            if ($this->searchKeywordsInText($educationKeywords, $line)) {

                $segment[] = $line;
                //$i++;
                $flag = true;

                while ($i < count($lines)) {

                    $row = $lines[$i];

                    if (
                        //!$this->searchKeywordsInText($projectKeywords, $row) &&
                        ! $this->searchKeywordsInText($skillKeywords, $row) &&
                        ! $this->searchKeywordsInText($accomplishmentKeywords, $row) &&
                        ! $this->searchKeywordsInText($experienceKeywords, $row)
                    ) {
                        $segment[] = $row;
                    } else {
                        break;
                    }
                    $i++;
                }
            }

            if ($flag) {
                break;
            }
        }

        return $segment;
    }

    public function getExperienceSegment($text)
    {

        $segment = [];

        $lines = $this->getLines($text);

        //dd($lines);

        $educationKeywords = $this->getEducationSegmentKeywords();
        $degreeKeywords = $this->getDegreeSegmentKeywords();
        //$projectKeywords        = $this->getProjectSegmentKeywords();
        $skillKeywords = $this->getSkillSegmentKeywords();
        $accomplishmentKeywords = $this->getAccomplishmentSegmentKeywords();
        $experienceKeywords = $this->getExperienceSegmentKeywords();

        $i = 0;
        foreach ($lines as $line) {

            $i++;
            $flag = false;

            if ($this->searchKeywordsInText($experienceKeywords, $line)) {

                $segment[] = $line;
                //$i++;
                $flag = true;

                while ($i < count($lines)) {

                    $row = $lines[$i];

                    if (
                        //!$this->searchKeywordsInText($projectKeywords, $row) &&
                        ! $this->searchKeywordsInText($skillKeywords, $row) &&
                        ! $this->searchKeywordsInText($accomplishmentKeywords, $row) &&
                        ! $this->searchKeywordsInText($educationKeywords, $row) &&
                        ! $this->searchKeywordsInText($degreeKeywords, $row)
                    ) {
                        $segment[] = $row;
                        //                        echo $row;
                        //                        echo "<br>";
                    } else {
                        break;
                    }
                    $i++;
                }
            }

            if ($flag) {
                break;
            }
        }

        return $segment;
    }

    public function parseEducationSegment($text)
    {

        $datesFound = [];
        $degreesFound = [];
        $schoolsFound = [];

        $education = [];

        $educationSegment = $this->getEducationSegment($text);

        //dd($educationSegment);

        $pattern = $this->dateRegex();
        $degrees = []; //Degree::getDegrees();
        $degreesAssoc = []; //Degree::getDegreesАssoc();
        $universities = []; //University::getUniversities();

        $datesSegments = [];
        $i = 0;

        foreach ($educationSegment as $line) {

            $datesSegments[$i][] = $line;

            preg_match_all($pattern, $line, $matches);

            if (count($matches) > 0) {

                if (isset($matches[0][0])) {
                    $datesFound[] = $matches[0][0];

                    $i++;
                    $datesSegments[$i][] = $line;

                    array_pop($datesSegments[$i - 1]);
                }
            }

        }

        array_shift($datesSegments);

        for ($i = 0; $i < count($datesSegments); $i++) {

            $flag = false;

            for ($j = 0; $j < count($datesSegments[$i]); $j++) {

                foreach ($degrees as $degree) {

                    if (strpos(ucwords($datesSegments[$i][$j]), $degree) > -1) {
                        $degreesFound[] = $degree;
                        $flag = true;
                        break;
                    }
                }

                if ($flag) {
                    break;
                }
            }

            if (! $flag) {
                $degreesFound[] = '';
            }
        }

        //dd($datesSegments);

        for ($i = 0; $i < count($datesSegments); $i++) {

            $flag = false;

            for ($j = 0; $j < count($datesSegments[$i]); $j++) {

                //var_dump(preg_replace("/(?![.=$'€%-])\p{P}/u", "", $datesSegments[$i][$j]));

                foreach ($universities as $university) {

                    if (strpos(preg_replace("/(?![.=$'€%-])\p{P}/u", '', strtolower($datesSegments[$i][$j])), str_replace('"', '', strtolower($university))) > -1) {
                        $schoolsFound[] = $university;
                        $flag = true;
                        break;
                    }
                }
            }

            if (! $flag) {

                for ($j = 0; $j < count($datesSegments[$i]); $j++) {

                    $entities = NLP::spacy_entities($datesSegments[$i][$j]);

                    if (! empty($entities)) {
                        //dd($entities);
                        if (isset($entities['ORG'])) {
                            $schoolsFound[] = $entities['ORG'][0];
                            $flag = true;
                            break;
                        }
                    }
                }
            }

            if (! $flag) {
                $schoolsFound[] = '';
            }
        }

        $i = 0;
        foreach ($datesFound as $date) {

            $education[$i]['date'] = $date;
            $education[$i]['degree'] = (isset($degreesFound[$i]) && isset($degreesAssoc[$degreesFound[$i]])) ? $degreesAssoc[$degreesFound[$i]] : '';
            $education[$i]['university'] = isset($schoolsFound[$i]) ? $schoolsFound[$i] : '';

            $i++;
        }

        return $education;
    }

    public function parseExperienceSegment($text)
    {

        $datesFound = [];
        $positionsFound = [];
        $employersFound = [];

        $positions = []; //Position::getPositions();
        $employers = []; //Employer::getEmployers();
        //dd($employers);

        $experience = [];

        $experienceSegment = $this->getExperienceSegment($text);
        //dd($experienceSegment);

        $pattern = $this->dateRegex();

        $datesSegments = [];
        $i = 0;

        foreach ($experienceSegment as $line) {

            $datesSegments[$i][] = $line;

            preg_match_all($pattern, $line, $matches);

            if (count($matches) > 0) {

                if (isset($matches[0][0])) {
                    $datesFound[] = $matches[0][0];

                    $i++;
                    $datesSegments[$i][] = $line;

                    array_pop($datesSegments[$i - 1]);
                }
            }
        }

        array_shift($datesSegments);

        for ($i = 0; $i < count($datesSegments); $i++) {

            $flag = false;

            for ($j = 0; $j < count($datesSegments[$i]); $j++) {

                foreach ($positions as $position) {

                    if (strpos(ucwords($datesSegments[$i][$j]), $position) > -1) {
                        $positionsFound[] = $position;
                        $flag = true;
                        break;
                    }
                }

                if ($flag) {
                    break;
                }
            }

            if (! $flag) {
                $positionsFound[] = '';
            }
        }

        $companyKeywords = ['name of employer', 'company', 'employer', 'organization'];
        $replace = ['', '', '', ''];

        for ($i = 0; $i < count($datesSegments); $i++) {

            $flag = false;

            for ($j = 0; $j < count($datesSegments[$i]); $j++) {

                //echo $datesSegments[$i][$j];
                //echo "<br>";
                foreach ($companyKeywords as $comopanyKeyword) {

                    if (strpos(strtolower($datesSegments[$i][$j]), $comopanyKeyword) > -1) {
                        $employersFound[] = preg_replace("/(?![.=$'€%-])\p{P}/u", '', ucwords(trim(str_replace($companyKeywords, $replace, strtolower($datesSegments[$i][$j])))));

                        $flag = true;
                        break;
                    }
                }
                if ($flag) {
                    break;
                }
            }

            if (! $flag) {

                for ($j = 0; $j < count($datesSegments[$i]); $j++) {

                    if ($flag) {
                        break;
                    } else {

                        $entities = NLP::spacy_entities($datesSegments[$i][$j], 'en');

                        if (! empty($entities)) {

                            //var_dump($entities);

                            if (isset($entities['ORG'])) {
                                $employersFound[] = $entities['ORG'][0];
                                $flag = true;
                                break;
                            }
                        }
                    }

                    if ($flag) {
                        break;
                    } else {

                        foreach ($employers as $employer) {

                            if (strpos(strtolower($datesSegments[$i][$j]), strtolower(trim($employer))) > -1) {
                                $employersFound[] = $employer;
                                $flag = true;
                                break;
                            }
                        }
                    }
                }
            }

            if (! $flag) {
                $employersFound[] = '';
            }
        }

        $i = 0;
        foreach ($datesFound as $date) {

            $experience[$i]['date'] = $date;
            $experience[$i]['position'] = isset($positionsFound[$i]) ? $positionsFound[$i] : '';
            $experience[$i]['company'] = isset($employersFound[$i]) ? $employersFound[$i] : '';

            $i++;
        }

        return $experience;
    }

    /* NORMALIZE */

    public function normalizeName($name)
    {

        $search = ['Name', ':'];
        $replace = ['', ''];

        $name = str_replace($search, $replace, $name);

        return ucwords(strtolower($name));
    }

    public function normalizeBirthDay($birthday)
    {

        $birthday = str_replace(['/'], ['.'], $birthday);

        return Carbon::parse($birthday)->format('d.m.Y');
    }

    public function normalizePosition($name)
    {

        return ucwords(strtolower($name));
    }

    public function dateRegex()
    {

        $patterns = [];

        $patterns[] = '(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})[\s–\-\—]+(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})';
        $patterns[] = '(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sept(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})[\s–\-\—]+(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sept(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})';
        $patterns[] = '(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})[\s­]+(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})';
        $patterns[] = '(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})[\s­]+(till now)';
        $patterns[] = '(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})[\s–\-]+(till now)';
        $patterns[] = '(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})[\s–\-]+(now)';
        $patterns[] = '(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{4})[\s–\-]+(ongoing)';
        $patterns[] = '([0-9]{2})\/([0-9]{2})\/([0-9]{4})[\s–\-]+([0-9]{2})\/([0-9]{2})\/([0-9]{4})';
        $patterns[] = '([0-9]{2})\.([0-9]{2})\.([0-9]{4})[\s–\-]+([0-9]{2})\.([0-9]{2})\.([0-9]{4})';
        $patterns[] = '([0-9]{2})\/([0-9]{4})[\s–\-]+([0-9]{2})\/([0-9]{4})';
        $patterns[] = '([0-9]{2})\.([0-9]{4})[\s–\-]+([0-9]{2})\.([0-9]{4})';
        $patterns[] = '([0-9]{2})\/([0-9]{4})[\s–\-]+(present)';
        $patterns[] = '([0-9]{2})\.([0-9]{4})[\s–\-]+(present)';
        $patterns[] = '([0-9]{2})\/([0-9]{4})[\s–\-]+(now)';
        $patterns[] = '([0-9]{2})\/([0-9]{4})[\s–\-]+(till now)';
        $patterns[] = '([0-9]{2})\.([0-9]{4})[\s–\-]+(till now)';
        $patterns[] = '([0-9]{2})\/([0-9]{4})[\s–\-]+(till today)';
        $patterns[] = '([0-9]{2})\.([0-9]{4})[\s–\-]+(till today)';
        $patterns[] = '([0-9]{4})[\s–\-]+([0-9]{4})';
        $patterns[] = '([0-9]{4})[\s–\—]+([0-9]{4})';
        $patterns[] = '([0-9]{4}) to ([0-9]{4})';
        $patterns[] = '([0-9]{4})[\s–\-]+(present)';
        $patterns[] = '([0-9]{4})[\s–\-]+(till now)';
        $patterns[] = '([0-9]{4})[\s–\-]+(until now)';
        $patterns[] = '([0-9]{4})[\s–\-]+(till today)';
        $patterns[] = '([0-9]{4})[\s–\-]+(still)';
        $patterns[] = '([0-9]{4})[\s–\-]+(ongoing)';

        $patterns[] = '([0-9]{2})\.[\s]([0-9]{4})[\s–\-]+([0-9]{2})\.[\s]([0-9]{4})';
        $patterns[] = '([0-9]{2})\/([0-9]{2})\/([0-9]{4})[ to ]+([0-9]{2})\/([0-9]{2})\/([0-9]{4})';
        $patterns[] = '([0-9]{1})\/([0-9]{2})\/([0-9]{4})[\s]+(to now)';
        //$patterns[] = '([0-9]{4})';

        $pattern = '/'.implode('|', $patterns).'/i';

        return $pattern;
    }
}
