<?php
use Generated\Models\Test_tabl;

function getTest_tablModel(): Test_tabl
{
    return new Test_tabl();
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

function index_test_tabl(): void
{
    $model = getTest_tablModel();
    $items = $model->getAllWithRelations();
    require __DIR__ . '/../views/test_tabl/index.php';
}

function create_test_tabl(): void
{
    require __DIR__ . '/../views/test_tabl/create.php';
}

function store_test_tabl(): void
{
    $model = getTest_tablModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/test_tabl/create');
        exit;
    }
    
    if ($model->create($data)) {
        $_SESSION['success'] = 'Registro creado correctamente';
        header('Location: ' . BASE_PATH . '/crud/test_tabl');
        exit;
    } else {
        $_SESSION['error'] = 'Error al crear el registro';
        header('Location: ' . BASE_PATH . '/crud/test_tabl/create');
        exit;
    }
}

function edit_test_tabl(int $id): void
{
    $model = getTest_tablModel();
    $item = $model->getById($id);
    
    if (!$item) {
        $_SESSION['error'] = 'Registro no encontrado';
        header('Location: ' . BASE_PATH . '/crud/test_tabl');
        exit;
    }
    
    require __DIR__ . '/../views/test_tabl/edit.php';
}

function update_test_tabl(int $id): void
{
    $model = getTest_tablModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/test_tabl/edit/' . $id);
        exit;
    }
    
    if ($model->update($id, $data)) {
        $_SESSION['success'] = 'Registro actualizado correctamente';
        header('Location: ' . BASE_PATH . '/crud/test_tabl');
        exit;
    } else {
        $_SESSION['error'] = 'Error al actualizar el registro';
        header('Location: ' . BASE_PATH . '/crud/test_tabl/edit/' . $id);
        exit;
    }
}

function delete_test_tabl(int $id): void
{
    $model = getTest_tablModel();
    
    if ($model->delete($id)) {
        $_SESSION['success'] = 'Registro eliminado correctamente';
    } else {
        $_SESSION['error'] = 'Error al eliminar el registro';
    }
    
    header('Location: ' . BASE_PATH . '/crud/test_tabl');
    exit;
}