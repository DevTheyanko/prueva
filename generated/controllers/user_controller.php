<?php
use Generated\Models\User;

function getUserModel(): User
{
    return new User();
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

function index_user(): void
{
    $model = getUserModel();
    $items = $model->getAllWithRelations();
    require __DIR__ . '/../views/user/index.php';
}

function create_user(): void
{
    require __DIR__ . '/../views/user/create.php';
}

function store_user(): void
{
    $model = getUserModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/user/create');
        exit;
    }
    
    if ($model->create($data)) {
        $_SESSION['success'] = 'Registro creado correctamente';
        header('Location: ' . BASE_PATH . '/crud/user');
        exit;
    } else {
        $_SESSION['error'] = 'Error al crear el registro';
        header('Location: ' . BASE_PATH . '/crud/user/create');
        exit;
    }
}

function edit_user(int $id): void
{
    $model = getUserModel();
    $item = $model->getById($id);
    
    if (!$item) {
        $_SESSION['error'] = 'Registro no encontrado';
        header('Location: ' . BASE_PATH . '/crud/user');
        exit;
    }
    
    require __DIR__ . '/../views/user/edit.php';
}

function update_user(int $id): void
{
    $model = getUserModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/user/edit/' . $id);
        exit;
    }
    
    if ($model->update($id, $data)) {
        $_SESSION['success'] = 'Registro actualizado correctamente';
        header('Location: ' . BASE_PATH . '/crud/user');
        exit;
    } else {
        $_SESSION['error'] = 'Error al actualizar el registro';
        header('Location: ' . BASE_PATH . '/crud/user/edit/' . $id);
        exit;
    }
}

function delete_user(int $id): void
{
    $model = getUserModel();
    
    if ($model->delete($id)) {
        $_SESSION['success'] = 'Registro eliminado correctamente';
    } else {
        $_SESSION['error'] = 'Error al eliminar el registro';
    }
    
    header('Location: ' . BASE_PATH . '/crud/user');
    exit;
}