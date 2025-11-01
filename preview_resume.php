<?php
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_FILES['resume'])) {
    $uploadDir = __DIR__ . "/uploads/resumes/";
    $originalName = basename($_FILES['resume']['name']);
    $tmpPath = $_FILES['resume']['tmp_name'];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    $newFileName = uniqid() . '.' . $ext;
    $targetPath = $uploadDir . $newFileName;

    // Move uploaded Word file to server
    if (move_uploaded_file($tmpPath, $targetPath)) {
        // If it’s Word, convert to PDF using LibreOffice
        if (in_array($ext, ['doc', 'docx'])) {
            $pdfName = uniqid() . '.pdf';
            $outputPath = $uploadDir;

            // Run LibreOffice command
            $cmd = "libreoffice --headless --convert-to pdf \"$targetPath\" --outdir \"$outputPath\"";
            exec($cmd, $output, $result);

            if ($result === 0) {
                // Conversion success: remove the Word file, store the PDF
                unlink($targetPath);
                $finalFile = $pdfName;

                // ✅ Now store $finalFile in your DB instead!
                echo "Converted to PDF successfully: $finalFile";
            } else {
                echo "Conversion failed.";
            }
        } else if ($ext === 'pdf') {
            // Already PDF, keep as is
            $finalFile = $newFileName;
            echo "PDF uploaded: $finalFile";
        } else {
            echo "Invalid file type. Only PDF, DOC, DOCX allowed.";
        }
    } else {
        echo "Upload failed.";
    }
}
?>