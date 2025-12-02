<?php
use Generated\Models\Student;

function getStudentModel(): Student
{
    return new Student();
}

function cleanFormData(array $data): array
{
    $unwanted = ['submit', 'csrf_token', '_method'];
    foreach ($unwanted as $field) {
        unset($data[$field]);
    }
    
    return array_filter($data, function($value) {
        return $value !== '';
    });
}

function index_student(): void
{
    $model = getStudentModel();
    $items = $model->getAllWithRelations();
    require __DIR__ . '/../views/student/index.php';
}

function create_student(): void
{
    require __DIR__ . '/../views/student/create.php';
}

function store_student(): void
{
    $model = getStudentModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/student/create');
        exit;
    }
    
    if ($model->create($data)) {
        $_SESSION['success'] = 'Registro creado correctamente';
        header('Location: ' . BASE_PATH . '/crud/student');
        exit;
    } else {
        $_SESSION['error'] = 'Error al crear el registro';
        header('Location: ' . BASE_PATH . '/crud/student/create');
        exit;
    }
}

function edit_student(int $id): void
{
    $model = getStudentModel();
    $item = $model->getById($id);
    
    if (!$item) {
        $_SESSION['error'] = 'Registro no encontrado';
        header('Location: ' . BASE_PATH . '/crud/student');
        exit;
    }
    
    require __DIR__ . '/../views/student/edit.php';
}

function update_student(int $id): void
{
    $model = getStudentModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/student/edit/' . $id);
        exit;
    }
    
    if ($model->update($id, $data)) {
        $_SESSION['success'] = 'Registro actualizado correctamente';
        header('Location: ' . BASE_PATH . '/crud/student');
        exit;
    } else {
        $_SESSION['error'] = 'Error al actualizar el registro';
        header('Location: ' . BASE_PATH . '/crud/student/edit/' . $id);
        exit;
    }
}

function delete_student(int $id): void
{
    $model = getStudentModel();
    
    if ($model->delete($id)) {
        $_SESSION['success'] = 'Registro eliminado correctamente';
    } else {
        $_SESSION['error'] = 'Error al eliminar el registro';
    }
    
    header('Location: ' . BASE_PATH . '/crud/student');
    exit;
}