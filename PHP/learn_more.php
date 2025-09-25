<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn More - Online Enrollment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 30px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #3498db, #2ecc71, #f39c12);
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eaeaea;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        h1 i {
            color: #3498db;
        }
        
        h2 {
            color: #2c3e50;
            margin: 30px 0 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        h2 i {
            color: #2ecc71;
            font-size: 1.2em;
        }
        
        h3 {
            color: #495057;
            margin: 20px 0 10px;
            font-weight: 500;
            padding-left: 10px;
            border-left: 3px solid #f39c12;
        }
        
        ul {
            margin: 10px 0 20px 30px;
        }
        
        li {
            margin-bottom: 10px;
            position: relative;
            padding-left: 5px;
        }
        
        li::before {
            content: '•';
            color: #3498db;
            font-weight: bold;
            display: inline-block;
            width: 1em;
            margin-left: -1em;
        }
        
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }
        
        .card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #3498db;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .card h3 {
            border-left: none;
            padding-left: 0;
            margin-top: 0;
            color: #2c3e50;
        }
        
        .btn-container {
            margin-top: 40px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .btn-apply {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
            box-shadow: 0 3px 10px rgba(52, 152, 219, 0.3);
        }
        
        .btn-apply:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
            background: linear-gradient(135deg, #2980b9, #3498db);
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(108, 117, 125, 0.3);
        }
        
        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #eaeaea, transparent);
            margin: 30px 0;
        }
        
        .info-box {
            background: #e8f4fc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #3498db;
        }
        
        .info-box p {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-box i {
            color: #3498db;
            font-size: 1.2em;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 15px;
                padding: 25px;
            }
            
            .btn-container {
                flex-direction: column;
            }
            
            .btn-apply, .back-link {
                justify-content: center;
            }
            
            .card-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-graduation-cap"></i> Learn More About Our Enrollment Process</h1>
        
        <div class="info-box">
            <p><i class="fas fa-info-circle"></i> Our online enrollment system makes it easy to apply from anywhere. Follow the steps below to get started.</p>
        </div>
        
        <h2><i class="fas fa-book-open"></i> Academic Programs</h2>
        <div class="card-container">
            <div class="card">
                <h3>Technology Programs</h3>
                <ul>
                    <li>BS Computer Science</li>
                    <li>BS Information Technology</li>
                </ul>
            </div>
            <div class="card">
                <h3>Business & Hospitality</h3>
                <ul>
                    <li>BS Hospitality Management</li>
                    <li>BS Business Administration</li>
                </ul>
            </div>
            <div class="card">
                <h3>Vocational Programs</h3>
                <ul>
                    <li>Technical-Vocational Programs</li>
                    <li>Short-term Certificate Courses</li>
                </ul>
            </div>
        </div>
        
        <div class="section-divider"></div>
        
        <h2><i class="fas fa-award"></i> Scholarship Opportunities</h2>
        <ul>
            <li><strong>Academic Excellence Scholarship</strong> - For students with outstanding academic records</li>
            <li><strong>Financial Assistance Grant</strong> - Support for students with financial need</li>
            <li><strong>Sports and Arts Scholarship</strong> - For talented athletes and artists</li>
            <li><strong>Government Aid Programs</strong> - Available for eligible students</li>
        </ul>
        
        <div class="section-divider"></div>
        
        <h2><i class="fas fa-file-alt"></i> Required Documents</h2>
        
        <h3>For Freshmen Applicants</h3>
        <ul>
            <li>Form 138 / High School Transcript of Records</li>
            <li>Birth Certificate (NSO/PSA)</li>
            <li>Certificate of Good Moral Character</li>
            <li>Two (2) recent 2x2 ID Photos</li>
        </ul>
        
        <h3>For Transfer Students</h3>
        <ul>
            <li>Official Transcript of Records from Previous College</li>
            <li>Certificate of Good Moral Character</li>
            <li>Honorable Dismissal / Transfer Credentials</li>
            <li>Two (2) recent 2x2 ID Photos</li>
        </ul>
        
        <div class="btn-container">
            <a href="OnlineApplication.php" class="btn-apply">
                <i class="fas fa-pencil-alt"></i> Begin Online Application
            </a>
            <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Return to Homepage
            </a>
        </div>
    </div>

    <script>
        // Simple animation for cards on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
            
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s, transform 0.5s';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>