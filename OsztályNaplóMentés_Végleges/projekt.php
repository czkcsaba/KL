<?php
/** 
* @author Oszaczki Csaba 
*/

session_start();
require_once("classroom-data.php");

require_once("functions.php");

// Iskola megjelenítése
if (empty($_SESSION["school"])) {
    $_SESSION["school"] = generateSchool();
}

if (isset($_POST['regenerate'])) {
    $_SESSION['school'] = generateSchool();
}

$school = $_SESSION["school"];
$class = $_GET['class'] ?? 'all';

require_once("index.php");

// Iskolaátlag megjelenítése
$showSchoolAverages = isset($_POST['show_school_averages']) ? true : false;
showAverages($school, $class, $showSchoolAverages);

// Adatok mentése CSV fájlba
saveToCSVFile($school, $class);

// Iskolaátlag mentése CSV fájlba
if (isset($_POST['export_school_averages'])) {
    saveSchoolAveragesToCSV($school);
}

// Legjobb és legrosszabb osztályok mentése CSV fájlba
if (isset($_POST['save_best_and_worst_classes'])) {
    saveBestAndWorstClassesToCSV($school);
}

// Legjobb és legrosszabb osztályok megjelenítése ÖSSZESÍTVE ÉS TANTÁRGYANKÉNT
if (isset($_POST['show_best_worst_classes']) && $class == "all") {
    
    // Összesített legjobb és legrosszabb osztály
    $overallResult = getBestAndWorstOverallClass($school);
    echo "<h2>Összesített eredmények</h2>";
    echo "<p>Legjobb osztály összesítésben: " . $overallResult['best'] . "</p>";
    echo "<p>Leggyengébb osztály összesítésben: " . $overallResult['worst'] . "</p>";

    echo "<h2>Legjobb és leggyengébb osztályok tantárgyanként</h2>";
    
    foreach (DATA['subjects'] as $subject) {
        $result = getBestAndWorstClassPerSubject($school);
        echo "<p>$subject: Legjobb osztály: " . $result[$subject]['best'] . ", Leggyengébb osztály: " . $result[$subject]['worst'] . "</p>";
    }
}

// Diákok rangsorának mentése CSV fájlba
if (isset($_POST['export_ranking'])) {
    if ($class === 'all') {
        $ranking = rankStudentsSchoolWide($school,$class);
        saveRankingToCSV($ranking, "all");
    } else {
        $ranking = rankStudentsInClass($school[$class]);
        saveRankingToCSV($ranking, "$class");
    }
}

// Diákok rangsorának megjelenítése ÖSSZESÍTVE ÉS OSZTÁLYONKÉNT
if (isset($_POST['show_ranking'])){
    rankStudentsSchoolWide($school,$class);
}

// Táblázat megjelenítése
if ($class === 'all') {
    foreach ($school as $className => $students) {
        displayStudentsTable($students, $className);
    }
} elseif (isset($school[$class])) {
    displayStudentsTable($school[$class], $class);
}