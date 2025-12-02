<?php
use Generated\Models\Teacher;

function getTeacherModel(): Teacher
{
    return new Teacher();
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

function index_teacher(): void
{
    $model = getTeacherModel();
    $items = $model->getAllWithRelations();
    require __DIR__ . '/../views/teacher/index.php';
}

function create_teacher(): void
{
    require __DIR__ . '/../views/teacher/create.php';
}

function store_teacher(): void
{
    $model = getTeacherModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/teacher/create');
        exit;
    }
    
    if ($model->create($data)) {
        $_SESSION['success'] = 'Registro creado correctamente';
        header('Location: ' . BASE_PATH . '/crud/teacher');
        exit;
    } else {
        $_SESSION['error'] = 'Error al crear el registro';
        header('Location: ' . BASE_PATH . '/crud/teacher/create');
        exit;
    }
}

function edit_teacher(int $id): void
{
    $model = getTeacherModel();
    $item = $model->getById($id);
    
    if (!$item) {
        $_SESSION['error'] = 'Registro no encontrado';
        header('Location: ' . BASE_PATH . '/crud/teacher');
        exit;
    }
    
    require __DIR__ . '/../views/teacher/edit.php';
}

function update_teacher(int $id): void
{
    $model = getTeacherModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/teacher/edit/' . $id);
        exit;
    }
    
    if ($model->update($id, $data)) {
        $_SESSION['success'] = 'Registro actualizado correctamente';
        header('Location: ' . BASE_PATH . '/crud/teacher');
        exit;
    } else {
        $_SESSION['error'] = 'Error al actualizar el registro';
        header('Location: ' . BASE_PATH . '/crud/teacher/edit/' . $id);
        exit;
    }
}

function delete_teacher(int $id): void
{
    $model = getTeacherModel();
    
    if ($model->delete($id)) {
        $_SESSION['success'] = 'Registro eliminado correctamente';
    } else {
        $_SESSION['error'] = 'Error al eliminar el registro';
    }
    
    header('Location: ' . BASE_PATH . '/crud/teacher');
    exit;
}