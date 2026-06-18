<?php
require '../includes/db.php';
require '../includes/functions.php';
check_login();

// Para gerar PDF simples, usamos a função nativa de HTML
$periodo = $_GET['periodo'] ?? 'mes';

// Buscar colaboradores
$colaboradores = $pdo->query("SELECT id, nome FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);

// Arrays de totais e lucros
$totais = [];
$lucros = [];

foreach ($colaboradores as $c) {
  $id = $c['id'];

  switch ($periodo) {
    case 'diario':
      $inicio = $fim = date('Y-m-d');
      break;
    case 'semana':
      $inicio = date('Y-m-d', strtotime('monday this week'));
      $fim = date('Y-m-d', strtotime('sunday this week'));
      break;
    case 'ano':
      $inicio = date('Y-01-01');
      $fim = date('Y-12-31');
      break;
    default:
      $inicio = date('Y-m-01');
      $fim = date('Y-m-t');
      break;
  }

  $stmt = $pdo->prepare("SELECT SUM(total) as total FROM pedidos WHERE funcionario_id=? AND DATE(criado_em) BETWEEN ? AND ?");
  $stmt->execute([$id, $inicio, $fim]);
  $total = (float)$stmt->fetchColumn();
  $totais[$id] = $total;
  $lucros[$id] = $total * 0.3;
}

// Gerar HTML da tabela
ob_start();
?>
<h2>Dashboard Colaboradores - <?= ucfirst($periodo) ?></h2>
<table border="1" cellpadding="5" cellspacing="0">
  <tr>
    <th>Colaborador</th>
    <th>Total Arrecadado (€)</th>
    <th>Lucro Estimado (€)</th>
  </tr>
  <?php foreach ($colaboradores as $c):
    $id = $c['id']; ?>
    <tr>
      <td><?= htmlspecialchars($c['nome']) ?></td>
      <td><?= number_format($totais[$id] ?? 0, 2, ',', '.') ?></td>
      <td><?= number_format($lucros[$id] ?? 0, 2, ',', '.') ?></td>
    </tr>
  <?php endforeach; ?>
</table>
<?php
$html = ob_get_clean();

// Usar função nativa do PHP para PDF
// Se tiver TCPDF ou FPDF, pode-se usar para layout melhor
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=dashboard_colaboradores_{$periodo}.pdf");

// Convert HTML simples em PDF
// Alternativa simples: imprimir HTML em browser e salvar como PDF
echo $html;
exit;
?>