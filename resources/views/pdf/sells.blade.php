<!DOCTYPE html>
<html>
	<head>
		<title>{{ $filename }}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	</head>
	<body>
		<div class="center bold fuente16">
			REPORTE DE VENTAS
		</div>
		<div class="center fuente10" style="padding-top: 3px; padding-bottom: 10px;">
			Impreso el: {{ \Carbon\Carbon::now()->format('d/m/Y \a \l\a\s H:i') }}
		</div>
		<table class="borde fuente12">
			<tbody>
				<tr>
					<td width="10%" class="bold borde right">
						TIENDA
					</td>
					<td width="50%" class="borde" style="padding-left: 3px;">
						{{ $store }}
					</td>
					<td width="10%" class="bold borde right">
						FECHAS
					</td>
					<td width="30%" class="borde" style="padding-left: 3px;">
						Del {{ $date_from }} al {{ $date_to }}
					</td>
				</tr>
			</tbody>
		</table>
		<table class="table" style="padding-top: 20px;">
			<tbody>
				<tr class="bold fuente12">
					<td width="5%" class="borde center">Nº</td>
					<td width="35%" class="borde center">Vendedor</td>
					<td width="35%" class="borde center">Cliente</td>
					<td width="15%" class="borde center">Fecha</td>
					<td width="10%" class="borde center">Total</td>
				</tr>
				@foreach ($sells as $i => $sell)
					<tr class="fuente10">
						<td class="borde center">{{ $i+1 }}</td>
						<td class="borde center">{{ $sell->user_name }}</td>
						<td class="borde center">{{ $sell->client_name }}</td>
						<td class="borde center">{{ \Carbon\Carbon::parse($sell->created_at)->format('d/m/Y H:i') }}</td>
						<td class="borde right">{{ number_format($sell->total_price,2,',','.') }}</td>
					</tr>
				@endforeach
				<tr class="fuente10">
					<td colspan="4" class="bold borde right">TOTAL</td>
					<td class="borde right">{{ number_format($total,2,',','.') }}</td>
				</tr>
			</tbody>
		</table>
		<script type="text/php">
			if (isset($pdf)) {
				$text = "{PAGE_NUM} - {PAGE_COUNT}";
				$size = 10;
				$font = $fontMetrics->getFont("Arial");
				$width = $fontMetrics->get_text_width($text, $font, $size) / 2;
				$x = $pdf->get_width() / 2;
				$y = $pdf->get_height() - 45;
				$pdf->page_text($x, $y, $text, $font, $size);
			}
		</script>
	</body>
	<style>
	@font-face {
		font-family: 'Elegance';
		font-weight: normal;
		font-style: normal;
		font-variant: normal;
		src: url({{ storage_path('fonts/Arial.ttf') }}) format('truetype');
	}
	@page {
		margin: 2cm;
	}
	body {
		font-family: Arial, sans-serif;
	}
	.center {
		text-align: center;
	}
	.right {
		text-align: right;
		padding-right: 3px;
	}
	.bold {
		font-weight: bold;
	}
	table {
		width: 100%;
		table-layout: fixed;
		margin: 0px;
	}
	table, td {
		border-collapse: collapse;
	}
	.fuente10 {
		font-size: 10px;
	}
	.fuente12 {
		font-size: 12px;
	}
	.fuente16 {
		font-size: 16px;
		text-decoration: underline;
	}
	.borde {
		border: 1px solid black;
	}
	</style>
</html>