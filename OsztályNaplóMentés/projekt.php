<?php 
/** 
* @author Oszaczki Csaba 
* 
*/ 

session_start(); 
require_once("classroom-data.php"); 

// Funkciók a generáláshoz 
function getRandomElement($array) { 
   return $array[array_rand($array)]; 
} 

function generateGrades($subjects) { 
   $grades = []; 
   foreach ($subjects as $subject) { 
       $num_grades = rand(0, 5); 
       if ($num_grades == 0) { 
           $grades[$subject] = []; // Ha nincs jegy, üres tömb legyen 
       } else { 
           $grades[$subject] = array_map(fn() => rand(1, 5), range(1, $num_grades)); 
       } 
   } 
   return $grades; 
} 

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

function generateSchool() { 
   $school = []; 
   foreach (DATA['classes'] as $class) { 
       $school[$class] = array_map(fn() => generateStudent($class), range(1, rand(10, 15))); 
   } 
   return $school; 
} 

if (empty($_SESSION["school"])) { 
   $_SESSION["school"] = generateSchool(); 
} 

if (isset($_POST['regenerate'])) { 
   $_SESSION['school'] = generateSchool(); 
} 

$school = $_SESSION["school"]; 
$class = $_GET['class'] ?? 'all'; 

require_once('index.php'); 

function displayStudentsTable($students) { 
   echo '<table class="student-table">'; 
   echo '<thead><tr><th>Név</th><th>Nem</th><th>Osztály</th>'; 
   foreach (DATA['subjects'] as $subject) { 
       echo "<th>$subject</th>"; 
   } 
   echo '</tr></thead><tbody>'; 

   foreach ($students as $student) { 
       echo '<tr>'; 
       echo "<td>{$student['name']}</td>"; 
       echo "<td>" . ($student['gender'] === 'men' ? 'Fiú' : 'Lány') . "</td>"; 
       echo "<td>{$student['class']}</td>"; 
       foreach (DATA['subjects'] as $subject) { 
           $grades = $student['grades'][$subject] ?? []; 
           if (empty($grades)) { 
               echo "<td></td>"; 
           } else { 
               echo "<td>" . implode(', ', $grades) . "</td>"; 
           } 
       } 
       echo '</tr>'; 
   } 

   echo '</tbody></table>'; 
} 

// Adatok mentése CSV fájlba
// Adatok mentése CSV fájlba
function saveToCSVFile($school, $class) {
    if (isset($_POST['export_csv'])) { 
        $filename = $class . "_students.csv"; 
        $csvFile = fopen($filename, "w"); 

        $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
        fputs($csvFile, $bom);

        // Fejléc
        fputcsv($csvFile, array_merge(['Név', 'Nem', 'Osztály'], DATA['subjects']), ";");

        if ($class === 'all') {
            foreach ($school as $className => $students) {
                foreach ($students as $student) {
                    $row = [
                        $student['name'],
                        $student['gender'] === 'men' ? 'M' : 'F',
                        $className
                    ];
                    foreach (DATA['subjects'] as $subject) {
                        $grades = $student['grades'][$subject] ?? [];
                        $row[] = empty($grades) ? '' : implode(', ', $grades);
                    }
                    fputcsv($csvFile, $row, ";");
                }
            }
        } elseif (isset($school[$class])) {
            foreach ($school[$class] as $student) {
                $row = [
                    $student['name'],
                    $student['gender'] === 'men' ? 'M' : 'F',
                    $class
                ];
                foreach (DATA['subjects'] as $subject) {
                    $grades = $student['grades'][$subject] ?? [];
                    $row[] = empty($grades) ? '' : implode(', ', $grades);
                }
                fputcsv($csvFile, $row, ";");
            }
        }

        fclose($csvFile);
        echo "<p>A diákok adatai sikeresen elmentve a '$filename' fájlba!</p>";
    }
}

saveToCSVFile($school,$class);

// Táblázat megjelenítése
function showTable($school,$class){
    if ($class === 'all') { 
    foreach ($school as $className => $students) { 
        echo "<h2>$className osztály</h2>"; 
        displayStudentsTable($students); 
    } 
    } elseif (isset($school[$class])) { 
    echo "<h2>$class osztály</h2>"; 
    displayStudentsTable($school[$class]); 
    }
}

showTable($school,$class);
?> 
