<?php
/** 
* @author Oszaczki Csaba 
*/ 

// Funkciók az adatok generálásához
function getRandomElement($array) {
    return $array[array_rand($array)];
}

// Iskola létrehozása
function generateSchool() {
    $school = [];
    foreach (DATA['classes'] as $class) {
        $school[$class] = array_map(fn() => generateStudent($class), range(1, rand(10, 15)));
    }
    return $school;
}

// Jegyek létrehozása
function generateGrades($subjects) {
    $grades = [];
    foreach ($subjects as $subject) {
        $num_grades = rand(0, 5);
        if ($num_grades == 0) {
            $grades[$subject] = [];
        } else {
            $grades[$subject] = array_map(fn() => rand(1, 5), range(1, $num_grades));
        }
    }
    return $grades;
}

// Diákok létrehozása
function generateStudent($class) {
    $lastname = getRandomElement(DATA['lastnames']);
    $gender = rand(0, 1) ? 'men' : 'women';
    $firstname = getRandomElement(DATA['firstnames'][$gender]);
    return [
        'name' => "$lastname $firstname",
        'class' => $class,
        'gender' => $gender,
        'grades' => generateGrades(DATA['subjects'])
    ];
}

// Táblázat megjelenítése
function displayStudentsTable($students, $class) {
    echo '<table class="student-table">';
    echo '<thead><tr><th>Név</th><th>Nem</th><th>Osztály</th>';
    
    // Fejléc: Tantárgyak oszlopai
    foreach (DATA['subjects'] as $subject) {
        echo "<th>$subject</th>";
    }
    echo '<th>Átlag</th></tr></thead><tbody>';

    // Diákok listázása
    $studentData = [];
    foreach ($students as $student) {
        echo '<tr>';
        echo "<td>{$student['name']}</td>";
        echo "<td>" . ($student['gender'] === 'men' ? 'Fiú' : 'Lány') . "</td>";
        echo "<td>{$student['class']}</td>";
        
        // Diák tantárgyainak megjelenítése
        $totalGrades = 0;
        $gradeCount = 0;
        foreach (DATA['subjects'] as $subject) {
            $grades = $student['grades'][$subject] ?? [];
            if (empty($grades)) {
                echo "<td></td>";
            } else {
                echo "<td>" . implode(', ', $grades) . "</td>";
                $totalGrades += array_sum($grades);
                $gradeCount += count($grades);
            }
        }
        $average = $gradeCount > 0 ? number_format($totalGrades / $gradeCount, 2) : '';
        echo "<td>$average</td>";
        $studentData[] = [
            'name' => $student['name'],
            'gender' => $student['gender'],
            'class' => $student['class'],
            'average' => $average
        ];
        echo '</tr>';
    }

    // Osztályátlag kiszámítása és megjelenítése
    echo '<tr><td>Osztályátlag</td><td></td><td>' . $class . '</td>';
    $subjectAverages = [];
    $overallTotal = 0; // Összes tantárgy átlagának összegzése
    $subjectCount = 0; // Összes tantárgy számlálása
    
    // Tantárgyankénti osztályátlag kiszámítása
    foreach (DATA['subjects'] as $subject) {
        $subjectTotal = 0;
        $subjectGradesCount = 0;
        
        foreach ($students as $student) {
            $grades = $student['grades'][$subject] ?? [];
            if (!empty($grades)) {
                $subjectTotal += array_sum($grades);
                $subjectGradesCount += count($grades);
            }
        }
        $subjectAverage = $subjectGradesCount > 0 ? number_format($subjectTotal / $subjectGradesCount, 2) : 'Nincs adat';
        $subjectAverages[] = $subjectAverage;

        // Összesített átlag számítása
        if (is_numeric($subjectAverage)) { // Csak számértékeket vegyünk figyelembe
            $overallTotal += $subjectAverage;
            $subjectCount++;
        }
    }

    // Az osztályátlagok sorának megjelenítése
    foreach ($subjectAverages as $subjectAverage) {
        echo "<td>$subjectAverage</td>";
    }

    // Az osztály összesített átlagának kiszámítása
    $overallAverage = $subjectCount > 0 ? number_format($overallTotal / $subjectCount, 2) : 'Nincs adat';
    echo "<td>$overallAverage</td>"; // Az utolsó cella: osztály összesített átlaga
    echo '</tr>';

    echo '</tbody>';
    echo '</table>';
}

// Tanuló átlagának kiszámítása
function calculateAverage($grades) {
    $totalGrades = 0;
    $gradeCount = 0;
    foreach (DATA['subjects'] as $subject) {
        $grade = $grades[$subject] ?? [];
        $totalGrades += array_sum($grade);
        $gradeCount += count($grade);
    }
    return $gradeCount > 0 ? number_format($totalGrades / $gradeCount, 2) : 'Nincs adat';
}

// Osztályátlagok kiszámítása tantárgyanként
function calculateClassAverageForSubject($className, $subject, $school) {
    $totalGrades = 0;
    $gradeCount = 0;
    foreach ($school[$className] as $student) {
        $grades = $student['grades'][$subject] ?? [];
        if (!empty($grades)) {
            $totalGrades += array_sum($grades);
            $gradeCount += count($grades);
        }
    }
    return $gradeCount > 0 ? number_format($totalGrades / $gradeCount, 2) : 'Nincs adat';
}

// Iskolaátlag kiszámítása tantárgyanként
function calculateSchoolAverage($school, $subject) {
    $totalGrades = 0;
    $gradeCount = 0;
    foreach ($school as $className => $students) {
        foreach ($students as $student) {
            $grades = $student['grades'][$subject] ?? [];
            if (!empty($grades)) {
                $totalGrades += array_sum($grades);
                $gradeCount += count($grades);
            }
        }
    }
    return $gradeCount > 0 ? number_format($totalGrades / $gradeCount, 2) : 'Nincs adat';
}

// Iskolaátlag megjelenítése
function showAverages($school, $class, $showSchoolAverages) {
    // Ha "Minden osztály" és kértük az iskolaátlagokat
    if ($class === 'all' && $showSchoolAverages) {
        echo "<h2>Iskola átlag tantárgyanként</h2>";
        foreach (DATA['subjects'] as $subject) {
            $subjectAverage = calculateSchoolAverage($school, $subject);
            echo "<p>$subject tantárgy átlag: $subjectAverage</p>";
        }
        echo "<br>";
    } elseif (isset($school[$class])) {
        // Ha osztály van kiválasztva, az átlagokat a táblázat alján jelenítjük meg
    }
}

// Adatok mentése CSV fájlba
function saveToCSVFile($school, $class) {
    if (!is_dir("./export")) {
        mkdir("./export");
    }
    if (isset($_POST['export_csv'])) {
        $filename = "./export/" . $class . "_students.csv";
        $csvFile = fopen($filename, "w");

        // UTF-8 BOM hozzáadása
        $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
        fputs($csvFile, $bom);

        // Fejléc
        fputcsv($csvFile, array_merge(['Név', 'Nem', 'Osztály'], DATA['subjects'], ['Átlag']), ";");

        if ($class === 'all') {
            // Minden osztály esetén
            foreach ($school as $className => $students) {
                foreach ($students as $student) {
                    $row = [
                        $student['name'],
                        $student['gender'] === 'men' ? 'M' : 'F',
                        $className
                    ];
                    $totalGrades = 0;
                    $gradeCount = 0;
                    foreach (DATA['subjects'] as $subject) {
                        $grades = $student['grades'][$subject] ?? [];
                        $row[] = empty($grades) ? '' : implode(', ', $grades);
                        if (!empty($grades)) {
                            $totalGrades += array_sum($grades);
                            $gradeCount += count($grades);
                        }
                    }
                    $average = $gradeCount > 0 ? number_format($totalGrades / $gradeCount, 2) : '';
                    $row[] = $average;
                    fputcsv($csvFile, $row, ";");
                }

                // Osztályátlag sor
                $classAverageRow = ['Osztályátlag', '', $className];
                $subjectAverages = [];
                $overallTotal = 0;
                $subjectCount = 0;

                foreach (DATA['subjects'] as $subject) {
                    $subjectTotal = 0;
                    $subjectGradesCount = 0;

                    foreach ($students as $student) {
                        $grades = $student['grades'][$subject] ?? [];
                        if (!empty($grades)) {
                            $subjectTotal += array_sum($grades);
                            $subjectGradesCount += count($grades);
                        }
                    }
                    $subjectAverage = $subjectGradesCount > 0 ? number_format($subjectTotal / $subjectGradesCount, 2) : 'Nincs adat';
                    $subjectAverages[] = $subjectAverage;

                    if (is_numeric($subjectAverage)) {
                        $overallTotal += $subjectAverage;
                        $subjectCount++;
                    }
                }

                $overallAverage = $subjectCount > 0 ? number_format($overallTotal / $subjectCount, 2) : 'Nincs adat';

                // Az osztályátlagokat hozzáadjuk a CSV sorhoz
                $classAverageRow = array_merge($classAverageRow, $subjectAverages, [$overallAverage]);
                fputcsv($csvFile, $classAverageRow, ";");
            }
        } elseif (isset($school[$class])) {
            // Kiválasztott osztály esetén
            foreach ($school[$class] as $student) {
                $row = [
                    $student['name'],
                    $student['gender'] === 'men' ? 'M' : 'F',
                    $class
                ];
                $totalGrades = 0;
                $gradeCount = 0;
                foreach (DATA['subjects'] as $subject) {
                    $grades = $student['grades'][$subject] ?? [];
                    $row[] = empty($grades) ? '' : implode(', ', $grades);
                    if (!empty($grades)) {
                        $totalGrades += array_sum($grades);
                        $gradeCount += count($grades);
                    }
                }
                $average = $gradeCount > 0 ? number_format($totalGrades / $gradeCount, 2) : '';
                $row[] = $average;
                fputcsv($csvFile, $row, ";");
            }

            // Osztályátlag sor
            $classAverageRow = ['Osztályátlag', '', $class];
            $subjectAverages = [];
            $overallTotal = 0;
            $subjectCount = 0;

            foreach (DATA['subjects'] as $subject) {
                $subjectTotal = 0;
                $subjectGradesCount = 0;

                foreach ($school[$class] as $student) {
                    $grades = $student['grades'][$subject] ?? [];
                    if (!empty($grades)) {
                        $subjectTotal += array_sum($grades);
                        $subjectGradesCount += count($grades);
                    }
                }
                $subjectAverage = $subjectGradesCount > 0 ? number_format($subjectTotal / $subjectGradesCount, 2) : 'Nincs adat';
                $subjectAverages[] = $subjectAverage;

                if (is_numeric($subjectAverage)) {
                    $overallTotal += $subjectAverage;
                    $subjectCount++;
                }
            }

            $overallAverage = $subjectCount > 0 ? number_format($overallTotal / $subjectCount, 2) : 'Nincs adat';

            // Az osztályátlagokat hozzáadjuk a CSV sorhoz
            $classAverageRow = array_merge($classAverageRow, $subjectAverages, [$overallAverage]);
            fputcsv($csvFile, $classAverageRow, ";");
        }

        fclose($csvFile);
        echo "<p>A diákok adatai sikeresen elmentve a '$filename' fájlba!</p>";
        echo "<br>";
    }
}

// Iskolaátlag mentése CSV fájlba
function saveSchoolAveragesToCSV($school) {
    if (!is_dir("./export")) {
        mkdir("./export");
    }

    $filename = "./export/school_averages.csv";
    $csvFile = fopen($filename, "w");

    // UTF-8 BOM hozzáadása
    $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
    fputs($csvFile, $bom);

    // Fejléc
    fputcsv($csvFile, ['Tantárgy', 'Átlag'], ";");

    // Tantárgyankénti átlag kiszámítása és mentése
    foreach (DATA['subjects'] as $subject) {
        $average = calculateSchoolAverage($school, $subject);
        fputcsv($csvFile, [$subject, $average], ";");
    }

    fclose($csvFile);

    echo "<p>Az iskolaátlagok sikeresen elmentve a '$filename' fájlba!</p>";
    echo "<br>";
}

// Legjobb és legrosszabb osztályok mentése CSV fájlba
function saveBestAndWorstClassesToCSV($school) {
    if (!is_dir("./export")) {
        mkdir("./export");
    }

    $filename = "./export/best_and_worst_classes.csv";
    $csvFile = fopen($filename, "w");

    // UTF-8 BOM hozzáadása
    $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
    fputs($csvFile, $bom);

    // Fejléc
    fputcsv($csvFile, ['Kategória', 'Tantárgy', 'Átlag'], ";");

    // Legjobb és leggyengébb osztály összesített adatai
    $overallResult = getBestAndWorstOverallClass($school);
    fputcsv($csvFile, ['Összesített', 'Legjobb osztály', $overallResult['best']], ";");
    fputcsv($csvFile, ['Összesített', 'Leggyengébb osztály', $overallResult['worst']], ";");

    // Legjobb és leggyengébb osztály tantárgyanként
    $resultPerSubject = getBestAndWorstClassPerSubject($school);
    foreach (DATA['subjects'] as $subject) {
        fputcsv($csvFile, ['Tantárgy', $subject, 'Legjobb osztály', $resultPerSubject[$subject]['best']], ";");
        fputcsv($csvFile, ['Tantárgy', $subject, 'Leggyengébb osztály', $resultPerSubject[$subject]['worst']], ";");
    }

    fclose($csvFile);

    echo "<p>A legjobb és leggyengébb osztályok adatai sikeresen elmentve a '$filename' fájlba!</p>";
    echo "<br>";
}

// Legjobb és legrosszabb osztály ÖSSZESÍTVE
function getBestAndWorstOverallClass($school) {
    $classAverages = [];

    foreach ($school as $className => $students) {
        $totalAverage = 0;
        $subjectCount = count(DATA['subjects']);

        foreach (DATA['subjects'] as $subject) {
            $subjectTotal = 0;
            $subjectCountPerClass = 0;

            foreach ($students as $student) {
                $grades = $student['grades'][$subject] ?? [];
                if (!empty($grades)) {
                    $subjectTotal += array_sum($grades);
                    $subjectCountPerClass += count($grades);
                }
            }

            $subjectAverage = $subjectCountPerClass > 0 ? $subjectTotal / $subjectCountPerClass : 0;
            $totalAverage += $subjectAverage;
        }

        $classAverages[$className] = $subjectCount > 0 ? $totalAverage / $subjectCount : 0;
    }

    arsort($classAverages);
    $bestClass = key($classAverages);
    asort($classAverages);
    $worstClass = key($classAverages);

    return [
        'best' => $bestClass,
        'worst' => $worstClass
    ];
}

// Legjobb és legrosszabb osztály TANTÁRGYANKÉNT
function getBestAndWorstClassPerSubject($school) {
    $bestAndWorst = [];

    foreach (DATA['subjects'] as $subject) {
        $subjectAverages = [];

        // Kiszámítjuk az átlagokat osztályonként
        foreach ($school as $className => $students) {
            $totalGrades = 0;
            $gradeCount = 0;
            
            foreach ($students as $student) {
                $grades = $student['grades'][$subject] ?? [];
                if (!empty($grades)) {
                    $totalGrades += array_sum($grades);
                    $gradeCount += count($grades);
                }
            }

            $average = $gradeCount > 0 ? $totalGrades / $gradeCount : 0;
            $subjectAverages[$className] = $average;
        }

        // Legjobb és leggyengébb osztály meghatározása
        arsort($subjectAverages);
        $bestClass = key($subjectAverages);
        asort($subjectAverages);
        $worstClass = key($subjectAverages);

        $bestAndWorst[$subject] = [
            'best' => $bestClass,
            'worst' => $worstClass
        ];
    }

    return $bestAndWorst;
}

// Diákok rangsorának mentése CSV fájlba
function saveRankingToCSV($ranking, $class) {
    if (!is_dir("./export")) {
        mkdir("./export");
    }

    // Fájlnév beállítása
    $filename = "./export/" . ($class === 'all' ? 'school_ranking' : $class . '_ranking') . ".csv";
    $csvFile = fopen($filename, "w");

    // UTF-8 BOM hozzáadása
    $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
    fputs($csvFile, $bom);

    // Fejléc írása
    fputcsv($csvFile, $class === "all" ? ['Helyezés', 'Név', 'Osztály', 'Átlag'] : ['Helyezés', 'Név', 'Átlag'], ";");

    // Ranglista adatok mentése
    foreach ($ranking as $index => $student) {
        fputcsv($csvFile, $class === "all" ?
            [
            $index + 1,                     // Helyezés
            $student['name'],               // Név
            $student['class'],              // Osztály
            $student['average']             // Átlag
            ] :
            [
            $index + 1,                     // Helyezés
            $student['name'],               // Név
            $student['average']             // Átlag
        ], ";");
    }

    fclose($csvFile);

    echo "<p>A rangsor sikeresen elmentve a '$filename' fájlba!</p>";
    echo "<br>";
}

// Diákok rangsorolása ÖSSZESÍTVE
function rankStudentsSchoolWide($school,$class) {
    // Ha "Minden osztály" menüpont van kiválasztva
    if ($class === 'all') {
        // Összes diák rangsorolása átlag szerint
        $students = [];
        foreach ($school as $className => $studentsList) {
            foreach ($studentsList as $student) {
                $students[] = [
                    'name' => $student['name'],
                    'class' => $className,
                    'average' => calculateAverage($student['grades'])
                ];
            }
        }

        // Rangsorolás átlag szerint
        usort($students, function($a, $b) {
            return $b['average'] <=> $a['average'];
        });

        // Készítünk egy HTML táblázatot a rangsorolt diákok számára
        echo "<h2>Iskolarangsor</h2>";
        echo '<table>';
        echo '<thead><tr><th>Rang</th><th>Név</th><th>Osztály</th><th style="padding-left:20px">Átlag</th></tr></thead><tbody>';
        foreach ($students as $index => $student) {
            echo '<tr>';
            echo '<td style="text-align: center">',$index + 1,'</td>';
            echo "<td style='text-align: center'>{$student['name']}</td>";
            echo "<td style='text-align: center'>{$student['class']}</td>";
            echo "<td style='text-align: center; padding-left:20px'>{$student['average']}</td>";
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '<br>';
        return $students;
    } else {
        // Ha nem "Minden osztály" menüpont, a rangsorolás csak az adott osztályra vonatkozik
        return rankStudentsInClass($school[$class]);
    }
}

// Diákok rangsorolása OSZTÁLYONKÉNT
function rankStudentsInClass($studentsList) {
    $students = [];        
    foreach ($studentsList as $student) {
        $students[] = [
            'name' => $student['name'],
            'average' => calculateAverage($student['grades'])
        ];
    }

    // Rangsorolás átlag szerint
    usort($students, function($a, $b) {
        return $b['average'] <=> $a['average'];
    });

    // Készítünk egy HTML táblázatot az osztály rangsorolt diákjai számára
    echo "<h2>Osztályrangsor</h2>";
    echo '<table>';
    echo '<thead><tr><th>Rang</th><th>Név</th><th style="padding-left:20px">Átlag</th></tr></thead><tbody>';
    foreach ($students as $index => $student) {
        echo '<tr>';
        echo '<td style="text-align: center">',$index + 1,'</td>';
        echo "<td style='text-align: center'>{$student['name']}</td>";
        echo "<td style='text-align: center; padding-left:20px'>{$student['average']}</td>";
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '<br>';
    return $students;
}