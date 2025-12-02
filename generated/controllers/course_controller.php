<?php
use Generated\Models\Course;

function getCourseModel(): Course
{
    return new Course();
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

function index_course(): void
{
    $model = getCourseModel();
    $items = $model->getAllWithRelations();
    require __DIR__ . '/../views/course/index.php';
}

function create_course(): void
{
    require __DIR__ . '/../views/course/create.php';
}

function store_course(): void
{
    $model = getCourseModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/course/create');
        exit;
    }
    
    if ($model->create($data)) {
        $_SESSION['success'] = 'Registro creado correctamente';
        header('Location: ' . BASE_PATH . '/crud/course');
        exit;
    } else {
        $_SESSION['error'] = 'Error al crear el registro';
        header('Location: ' . BASE_PATH . '/crud/course/create');
        exit;
    }
}

function edit_course(int $id): void
{
    $model = getCourseModel();
    $item = $model->getById($id);
    
    if (!$item) {
        $_SESSION['error'] = 'Registro no encontrado';
        header('Location: ' . BASE_PATH . '/crud/course');
        exit;
    }
    
    require __DIR__ . '/../views/course/edit.php';
}

function update_course(int $id): void
{
    $model = getCourseModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/course/edit/' . $id);
        exit;
    }
    
    if ($model->update($id, $data)) {
        $_SESSION['success'] = 'Registro actualizado correctamente';
        header('Location: ' . BASE_PATH . '/crud/course');
        exit;
    } else {
        $_SESSION['error'] = 'Error al actualizar el registro';
        header('Location: ' . BASE_PATH . '/crud/course/edit/' . $id);
        exit;
    }
}

function delete_course(int $id): void
{
    $model = getCourseModel();
    
    if ($model->delete($id)) {
        $_SESSION['success'] = 'Registro eliminado correctamente';
    } else {
        $_SESSION['error'] = 'Error al eliminar el registro';
    }
    
    header('Location: ' . BASE_PATH . '/crud/course');
    exit;
}