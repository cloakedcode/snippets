<?php

require('tcpdf/tcpdf.php');
require('fpdi/fpdi.php');

class PDF extends FPDI
{
	function Header() {}
	function Footer() {}

	static function StampPDFPages($pdf_file, $img_file)
	{
		$p = new self();
		$count = $p->setSourceFile($pdf_file);

		for ($i = 1; $i <= $count; $i++)
		{
			$p->addPage();
			$tpl = $p->importPage($i);
			$p->useTemplate($tpl);

			$tplsize = $p->getTemplateSize($tpl);

			$p->Image($img_file, 0, 0);
		}

		return $p;
	}
}

$pdf = PDF::StampPDFPages('test.pdf', 'test.png');
$pdf->Output('stamp.pdf', 'F');

?>