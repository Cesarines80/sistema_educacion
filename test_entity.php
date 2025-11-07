<?php

require 'vendor/autoload.php';

$kernel = new \App\Kernel('dev', true);
$kernel->boot();
$em = $kernel->getContainer()->get('doctrine')->getManager();

try {
    $subjects = $em->getRepository('App\Entity\Subject')->findAll();
    echo 'Subjects loaded: ' . count($subjects) . PHP_EOL;

    if (count($subjects) > 0) {
        $subject = $subjects[0];
        echo 'Subject name: ' . $subject->getName() . PHP_EOL;
        echo 'Academic grades: ' . $subject->getAcademicGrades()->count() . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
