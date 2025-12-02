<?php
use Generated\Models\Comment;

function getCommentModel(): Comment
{
    return new Comment();
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

function index_comment(): void
{
    $model = getCommentModel();
    $items = $model->getAllWithRelations();
    require __DIR__ . '/../views/comment/index.php';
}

function create_comment(): void
{
    require __DIR__ . '/../views/comment/create.php';
}

function store_comment(): void
{
    $model = getCommentModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/comment/create');
        exit;
    }
    
    if ($model->create($data)) {
        $_SESSION['success'] = 'Registro creado correctamente';
        header('Location: ' . BASE_PATH . '/crud/comment');
        exit;
    } else {
        $_SESSION['error'] = 'Error al crear el registro';
        header('Location: ' . BASE_PATH . '/crud/comment/create');
        exit;
    }
}

function edit_comment(int $id): void
{
    $model = getCommentModel();
    $item = $model->getById($id);
    
    if (!$item) {
        $_SESSION['error'] = 'Registro no encontrado';
        header('Location: ' . BASE_PATH . '/crud/comment');
        exit;
    }
    
    require __DIR__ . '/../views/comment/edit.php';
}

function update_comment(int $id): void
{
    $model = getCommentModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/comment/edit/' . $id);
        exit;
    }
    
    if ($model->update($id, $data)) {
        $_SESSION['success'] = 'Registro actualizado correctamente';
        header('Location: ' . BASE_PATH . '/crud/comment');
        exit;
    } else {
        $_SESSION['error'] = 'Error al actualizar el registro';
        header('Location: ' . BASE_PATH . '/crud/comment/edit/' . $id);
        exit;
    }
}

function delete_comment(int $id): void
{
    $model = getCommentModel();
    
    if ($model->delete($id)) {
        $_SESSION['success'] = 'Registro eliminado correctamente';
    } else {
        $_SESSION['error'] = 'Error al eliminar el registro';
    }
    
    header('Location: ' . BASE_PATH . '/crud/comment');
    exit;
}