<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseUrl = $baseUrl ?? rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$marcas = $marcas ?? [];
$veiculo = $veiculo ?? [];
$erros = $erros ?? [];
$modo = !empty($veiculo['id']) ? 'editar' : 'criar';
$tituloPagina = $titulo ?? ($modo === 'editar' ? 'Editar veículo' : 'Adicionar veículo');
$valor = static fn(string $chave, mixed $default = '') => htmlspecialchars((string) ($veiculo[$chave] ?? $default));
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina) ?></title>
    <style>
        body { margin:0; font-family:Arial, Helvetica, sans-serif; background:#f4f6fb; color:#182033; }
        main { max-width:980px; margin:0 auto; padding:32px 20px 48px; }
        .panel { background:#fff; border:1px solid #dbe3f0; border-radius:18px; padding:24px; box-shadow:0 10px 28px rgba(24,32,51,.08); }
        h1 { margin-top:0; }
        .muted { color:#52607a; }
        .errors { background:#fff1f2; border:1px solid #fecdd3; color:#9f1239; border-radius:12px; padding:14px 16px; margin-bottom:18px; }
        .grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:14px; }
        .field { display:flex; flex-direction:column; gap:6px; }
        .field.full { grid-column:1 / -1; }
        label { font-weight:700; }
        input, select, textarea { width:100%; padding:12px 14px; border-radius:10px; border:1px solid #cbd5e1; font:inherit; background:#fff; }
        textarea { min-height:140px; resize:vertical; }
        .actions { display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-top:18px; }
        .btn, .btn-ghost { display:inline-flex; align-items:center; justify-content:center; padding:11px 16px; border-radius:10px; text-decoration:none; font-weight:700; }
        .btn { background:#182033; color:#fff; border:1px solid #182033; }
        .btn-ghost { background:#fff; color:#182033; border:1px solid #cbd5e1; }
        .preview { margin-top:8px; display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
        .preview img { width:120px; height:80px; object-fit:cover; border-radius:10px; border:1px solid #dbe3f0; background:#edf2f7; }
        @media (max-width: 720px) { .grid { grid-template-columns:1fr; } }
    </style>
</head>
<body>
<main>
    <div class="panel">
        <h1><?= htmlspecialchars($tituloPagina) ?></h1>
        <p class="muted">Preenche todos os campos do veículo e faz upload de uma imagem em JPG, PNG ou WEBP.</p>

        <?php if (!empty($erros)): ?>
            <div class="errors">
                <?php foreach ($erros as $erro): ?>
                    <div><?= htmlspecialchars($erro) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($baseUrl . '/admin/veiculos/' . ($modo === 'editar' ? 'editar' : 'novo')) ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <?php if (!empty($veiculo['id'])): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars((string) $veiculo['id']) ?>">
            <?php endif; ?>

            <div class="grid">
                <div class="field">
                    <label for="marca_id">Marca</label>
                    <select id="marca_id" name="marca_id" required>
                        <option value="">Seleciona uma marca</option>
                        <?php foreach ($marcas as $marca): ?>
                            <option value="<?= htmlspecialchars((string) $marca['id']) ?>" <?= (string)($veiculo['marca_id'] ?? '') === (string) $marca['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($marca['nome'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="modelo">Modelo</label>
                    <input id="modelo" name="modelo" type="text" value="<?= $valor('modelo') ?>" required>
                </div>
                <div class="field">
                    <label for="ano">Ano</label>
                    <input id="ano" name="ano" type="number" min="1900" max="2100" value="<?= $valor('ano') ?>" required>
                </div>
                <div class="field">
                    <label for="quilometros">Quilómetros</label>
                    <input id="quilometros" name="quilometros" type="number" min="0" value="<?= $valor('quilometros', 0) ?>">
                </div>
                <div class="field">
                    <label for="combustivel">Combustível</label>
                    <select id="combustivel" name="combustivel" required>
                        <?php $combustivelAtual = (string) ($veiculo['combustivel'] ?? ''); ?>
                        <?php foreach (['Gasolina', 'Diesel', 'Híbrido', 'Elétrico', 'GPL'] as $opcao): ?>
                            <option value="<?= htmlspecialchars($opcao) ?>" <?= $combustivelAtual === $opcao ? 'selected' : '' ?>><?= htmlspecialchars($opcao) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="cilindrada">Cilindrada</label>
                    <input id="cilindrada" name="cilindrada" type="text" value="<?= $valor('cilindrada') ?>">
                </div>
                <div class="field">
                    <label for="preco">Preço</label>
                    <input id="preco" name="preco" type="number" min="0" step="0.01" value="<?= $valor('preco') ?>" required>
                </div>
                <div class="field">
                    <label for="imagem">Imagem</label>
                    <input id="imagem" name="imagem" type="file" accept="image/jpeg,image/png,image/webp">
                    <?php if (!empty($veiculo['imagem'])): ?>
                        <div class="preview">
                            <img src="<?= htmlspecialchars($baseUrl . '/uploads/' . $veiculo['imagem']) ?>" alt="Imagem atual">
                            <span class="muted">Imagem atual: <?= htmlspecialchars($veiculo['imagem']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="field full">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao"><?= htmlspecialchars((string) ($veiculo['descricao'] ?? '')) ?></textarea>
                </div>
            </div>

            <div class="actions">
                <button class="btn" type="submit"><?= $modo === 'editar' ? 'Guardar alterações' : 'Criar veículo' ?></button>
                <a class="btn-ghost" href="<?= htmlspecialchars($baseUrl . '/admin/veiculos') ?>">Voltar à lista</a>
            </div>
        </form>
    </div>
</main>
</body>
</html>
