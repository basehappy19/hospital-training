<?php 

function getCategory($conn) {
    $sql = 'SELECT id, categoryTitle FROM categories';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getCategoryById($conn, $id) {
    $sql = 'SELECT id, categoryTitle FROM categories WHERE id = :id;';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getCategoryWithCourses($conn, $view) {
    $sql = 'SELECT categories.id, categories.categoryTitle, c.courseTitle, c.id AS courseId 
            FROM categories 
            LEFT JOIN (
                SELECT courses.*, d.courseOpen
                FROM courses
                INNER JOIN course_details AS d ON courses.courseKey = d.courseKey';

    if ($view == 0) {   
        $sql .= ' AND d.courseOpen = 1';
    }

    $sql .= ') AS c ON c.courseCategoryId = categories.id;';

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $groupedResults = [];
    foreach ($results as $row) {
        $categoryId = $row['id'];
        if (!isset($groupedResults[$categoryId])) {
            $groupedResults[$categoryId] = [
                'id' => $categoryId,
                'categoryTitle' => $row['categoryTitle'],
                'courses' => [],
            ];
        }
        if ($row['courseTitle'] != null) {
            $groupedResults[$categoryId]['courses'][] = [
                'id' => $row['courseId'],
                'title' => $row['courseTitle']
            ];
        }
    }
    
    return array_values($groupedResults);
}