<head>
  <link rel="stylesheet" href="/css/auth.css">
  <style>
/* --- RESET --- */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
}

/* --- RESET PAGE --- */
.reset-page {
    background-color: #FFFCEC;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
}

.reset-container {
    width: 100%;
    max-width: 450px;
    text-align: center;
}

.reset-logo-circle {
    background-color: #5d2e1a;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto 15px;
    color: white;
    font-size: 35px;
}

.reset-header h1 {
    color: #5d2e1a;
    margin-bottom: 5px;
}

.reset-header p {
    color: #718096;
    font-size: 14px;
    margin-bottom: 30px;
}

.reset-card {
    background: white;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    text-align: left;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 8px;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #E2E8F0;
    background: #F7FAFC;
    border-radius: 10px;
}

.form-group textarea {
    resize: none;
    height: 100px;
}

.btn-submit {
    width: 100%;
    background: #5d2e1a;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 10px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
}

.success-message {
    background-color: #ECFDF5;
    border: 1px solid #10B981;
    color: #065F46;
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 20px;
    text-align: center;
}

.back-link {
    display: block;
    margin-top: 25px;
    color: #B7791F;
    font-weight: bold;
    text-decoration: none;
}
  </style>
</head>
@if(session('success'))
<div class="success-message">
    {{ session('success') }}
</div>
@endif


<form method="POST"
      action="{{ route('admin.reset.store') }}">

    @csrf

    <div class="form-group">
        <label>User ID</label>
        <input
            type="number"
            name="id_user"
            required>
    </div>


    <div class="form-group">
        <label>Full Name</label>

        <input
            type="text"
            name="full_name"
            placeholder="Enter your full name"
            required>
    </div>


    <div class="form-group">
        <label>Email</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            required>
    </div>


    <div class="form-group">
        <label>Why do you need a password reset?</label>

        <textarea
            name="reason"
            placeholder="Explain your reason..."
            required></textarea>
    </div>


    <button
        type="submit"
        class="btn-submit">

        Submit Request

    </button>

</form>