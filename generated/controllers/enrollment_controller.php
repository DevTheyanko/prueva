<?php
use Generated\Models\Enrollment;

function getEnrollmentModel(): Enrollment
{
    return new Enrollment();
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

function index_enrollment(): void
{
    $model = getEnrollmentModel();
    $items = $model->getAllWithRelations();
    require __DIR__ . '/../views/enrollment/index.php';
}

function create_enrollment(): void
{
    require __DIR__ . '/../views/enrollment/create.php';
}

function store_enrollment(): void
{
    $model = getEnrollmentModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/enrollment/create');
        exit;
    }
    
    if ($model->create($data)) {
        $_SESSION['success'] = 'Registro creado correctamente';
        header('Location: ' . BASE_PATH . '/crud/enrollment');
        exit;
    } else {
        $_SESSION['error'] = 'Error al crear el registro';
        header('Location: ' . BASE_PATH . '/crud/enrollment/create');
        exit;
    }
}

function edit_enrollment(int $id): void
{
    $model = getEnrollmentModel();
    $item = $model->getById($id);
    
    if (!$item) {
        $_SESSION['error'] = 'Registro no encontrado';
        header('Location: ' . BASE_PATH . '/crud/enrollment');
        exit;
    }
    
    require __DIR__ . '/../views/enrollment/edit.php';
}

function update_enrollment(int $id): void
{
    $model = getEnrollmentModel();
    $data = cleanFormData($_POST);
    
    if (empty($data)) {
        $_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/enrollment/edit/' . $id);
        exit;
    }
    
    if ($model->update($id, $data)) {
        $_SESSION['success'] = 'Registro actualizado correctamente';
        header('Location: ' . BASE_PATH . '/crud/enrollment');
        exit;
    } else {
        $_SESSION['error'] = 'Error al actualizar el registro';
        header('Location: ' . BASE_PATH . '/crud/enrollment/edit/' . $id);
        exit;
    }
}

function delete_enrollment(int $id): void
{
    $model = getEnrollmentModel();
    
    if ($model->delete($id)) {
        $_SESSION['success'] = 'Registro eliminado correctamente';
    } else {
        $_SESSION['error'] = 'Error al eliminar el registro';
    }
    
    header('Location: ' . BASE_PATH . '/crud/enrollment');
    exit;
}