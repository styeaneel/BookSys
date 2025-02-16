<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: main_menu.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Citation Generator - BookSys Library</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F9F9F9;
            margin: 0;
            padding: 0;
        }
        /* Sidebar */
        .sidebar {
            width: 250px;
            position: fixed;
            left: 0;
            height: 100%;
            background: linear-gradient(135deg, #D3B3F5, #5A2D82);
            padding-top: 55px;
            color: white;
        }
        .sidebar a {
            display: block;
            color: white;
            margin-bottom: 12px;
            padding: 15px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #482366;
        }
        /* Content */
        .content {
            margin-left: 280px;
            padding: 30px;
            font-size: 18px;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            margin: auto;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }
        .form-container input, .form-container select, .form-container button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-container button {
            background-color: #5A2D82;
            color: white;
            cursor: pointer;
        }
        .citation-output {
            margin-top: 20px;
            padding: 15px;
            background: #F4F4F4;
            border-left: 5px solid #5A2D82;
            font-size: 16px;
            display: none;
        }
        .btn-container {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            color: white;
            border-radius: 5px;
            font-size: 14px;
        }
        .copy-btn { background-color: #007BFF; }
        .print-btn { background-color: #5A2D82; }
        .pdf-btn { background-color: #28A745; }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="borrow_book.php">📚 Search Books</a>
        <a href="loan_history.php">📜 Loan History</a>
        <a href="citation_generator.php">📂 Citation Generator</a>
        <a href="check_and_pay_fines.php">Overdue Books & Fines</a>
        <a href="profile_settings.php">⚙️ Profile Settings</a>
    </div>

    <!-- Content -->
    <div class="content">
        <h2>📜 Citation Generator</h2>
        <div class="form-container">
            <form id="citationForm">
                <input type="text" id="url" placeholder="Enter Article/Book DOI or URL">
                <button type="button" onclick="fetchCitation()">Fetch Citation</button>

                <hr>
                <input type="text" id="author" placeholder="Author Name">
                <input type="text" id="title" placeholder="Book/Article Title">
                <input type="text" id="year" placeholder="Publication Year">
                <input type="text" id="publisher" placeholder="Publisher/Website">
                <select id="citationStyle">
                    <option value="APA">APA</option>
                    <option value="MLA">MLA</option>
                    <option value="Chicago">Chicago</option>
                    <option value="Harvard">Harvard</option>
                    <option value="IEEE">IEEE</option>
                </select>
                <button type="button" onclick="generateCitation()">Generate Citation</button>
            </form>
        </div>

        <div id="citationOutput" class="citation-output"></div>
        <div class="btn-container">
            <button class="btn copy-btn" onclick="copyCitation()">📋 Copy</button>
            <button class="btn print-btn" onclick="printCitation()">🖨 Print</button>
            <button class="btn pdf-btn" onclick="downloadCitationPDF()">📥 Download PDF</button>
        </div>
    </div>

    <script>
        async function fetchCitation() {
            let url = document.getElementById("url").value;
            let doi = extractDOI(url);

            if (!doi) {
                alert("No DOI found in URL.");
                return;
            }

            try {
                let response = await fetch(`https://api.crossref.org/works/${doi}`);
                let data = await response.json();

                if (data.message) {
                    document.getElementById("author").value = data.message.author ? data.message.author.map(a => a.family + ", " + a.given).join("; ") : "Unknown";
                    document.getElementById("title").value = data.message.title ? data.message.title[0] : "No Title";
                    document.getElementById("year").value = data.message.published["date-parts"][0][0] || "N/A";
                    document.getElementById("publisher").value = data.message.publisher || "N/A";
                }
            } catch (error) {
                alert("Failed to fetch citation details.");
            }
        }

        function extractDOI(url) {
            let doiPattern = /10\.\d{4,9}\/[-._;()/:A-Z0-9]+/i;
            let match = url.match(doiPattern);
            return match ? match[0] : null;
        }

        function generateCitation() {
            let author = document.getElementById("author").value;
            let title = document.getElementById("title").value;
            let year = document.getElementById("year").value;
            let publisher = document.getElementById("publisher").value;
            let style = document.getElementById("citationStyle").value;

            let citation = `${author} (${year}). *${title}*. ${publisher}.`;

            document.getElementById("citationOutput").innerText = citation;
            document.getElementById("citationOutput").style.display = "block";
        }

        function copyCitation() {
            navigator.clipboard.writeText(document.getElementById("citationOutput").innerText);
        }

        function printCitation() {
            window.print();
        }

        function downloadCitationPDF() {
            const { jsPDF } = window.jspdf;
            let doc = new jsPDF();
            doc.text(document.getElementById("citationOutput").innerText, 10, 10);
            doc.save("Citation.pdf");
        }
    </script>

</body>
</html>
