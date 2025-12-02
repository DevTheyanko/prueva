<?php
use Generated\Models\Post;

function getPostModel(): Post
{
    return new Post();
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

function index_post(): void
{
    $model = getPostModel();
    $items = $model->getAllWithRelations();
    require __DIR__ . '/../views/post/index.php';
}

function create_post(): void
{
    require __DIR__ . '/../views/post/create.php';
}

function store_post(): void
{
    $model = getPostModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/post/create');
        exit;
    }
    
    if ($model->create($data)) {
        $_SESSION['success'] = 'Registro creado correctamente';
        header('Location: ' . BASE_PATH . '/crud/post');
        exit;
    } else {
        $_SESSION['error'] = 'Error al crear el registro';
        header('Location: ' . BASE_PATH . '/crud/post/create');
        exit;
    }
}

function edit_post(int $id): void
{
    $model = getPostModel();
    $item = $model->getById($id);
    
    if (!$item) {
        $_SESSION['error'] = 'Registro no encontrado';
        header('Location: ' . BASE_PATH . '/crud/post');
        exit;
    }
    
    require __DIR__ . '/../views/post/edit.php';
}

function update_post(int $id): void
{
    $model = getPostModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/post/edit/' . $id);
        exit;
    }
    
    if ($model->update($id, $data)) {
        $_SESSION['success'] = 'Registro actualizado correctamente';
        header('Location: ' . BASE_PATH . '/crud/post');
        exit;
    } else {
        $_SESSION['error'] = 'Error al actualizar el registro';
        header('Location: ' . BASE_PATH . '/crud/post/edit/' . $id);
        exit;
    }
}

function delete_post(int $id): void
{
    $model = getPostModel();
    
    if ($model->delete($id)) {
        $_SESSION['success'] = 'Registro eliminado correctamente';
    } else {
        $_SESSION['error'] = 'Error al eliminar el registro';
    }
    
    header('Location: ' . BASE_PATH . '/crud/post');
    exit;
}