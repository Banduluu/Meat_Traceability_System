<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meat Tracking Kiosk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <form id="meatTrackingForm">
            <div class="main-content">
                <!-- Left Section: Meat Details -->
                <div class="section">
                    <div class="section-header">
                        <span class="section-icon">🥩</span>
                        <h2 class="section-title">Meat Details</h2>
                    </div>

                    <table class="meat-table">
                        <thead>
                            <tr>
                                <th>Type of Meat</th>
                                <th>Quantity (heads/sets)</th>
                                <th>Weight (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span class="meat-icon">🐷</span>
                                    <span>Pork</span>
                                </td>
                                <td>
                                    <input type="number" name="pork_qty" value="0" min="0">
                                </td>
                                <td>
                                    <input type="number" name="pork_weight" value="0" min="0" step="0.01">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="meat-icon">🐄</span>
                                    <span>Beef</span>
                                </td>
                                <td>
                                    <input type="number" name="beef_qty" value="0" min="0">
                                </td>
                                <td>
                                    <input type="number" name="beef_weight" value="0" min="0" step="0.01">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="meat-icon">🐃</span>
                                    <span>Carabeef</span>
                                </td>
                                <td>
                                    <input type="number" name="carabeef_qty" value="0" min="0">
                                </td>
                                <td>
                                    <input type="number" name="carabeef_weight" value="0" min="0" step="0.01">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="meat-icon">🐔</span>
                                    <span>Chicken</span>
                                </td>
                                <td>
                                    <input type="number" name="chicken_qty" value="0" min="0">
                                </td>
                                <td>
                                    <input type="number" name="chicken_weight" value="0" min="0" step="0.01">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Right Section: Origin & Destination -->
                <div class="section">
                    <div class="section-header">
                        <span class="section-icon">📍</span>
                        <h2 class="section-title">Origin & Destination</h2>
                    </div>

                    <div class="form-group form-row full">
                        <label>Point of Origin</label>
                        <input type="text" name="origin" placeholder="Enter origin location">
                    </div>

                    <div class="form-group form-row full">
                        <label>Destination</label>
                        <input type="text" name="destination" placeholder="Enter destination">
                    </div>

                    <div class="form-group form-row">
                        <div>
                            <label>Date Issued</label>
                            <input type="date" name="date_issued">
                        </div>
                        <div>
                            <label>Time Issued</label>
                            <input type="time" name="time_issued">
                        </div>
                    </div>

                    <div class="form-group form-row">
                        <div>
                            <label>Meat Inspector</label>
                            <input type="text" name="inspector" placeholder="Auto-filled Inspector Name">
                        </div>
                        <div>
                            <label>Owner / Dealer</label>
                            <input type="text" name="owner" placeholder="Enter name">
                        </div>
                    </div>

                    <div class="form-group form-row full">
                        <label>Received By</label>
                        <input type="text" name="received_by" placeholder="Enter name">
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="button-group">
                <button type="button" class="btn-cancel" onclick="cancelForm()">
                    <span class="icon">✕</span> Cancel
                </button>
                <button type="submit" class="btn-qr" onclick="generateQRCode(event)">
                    <span class="icon">🖨️</span> Generate MIC with QR Code
                </button>
                <button type="button" class="btn-save" onclick="saveRecord()">
                    <span class="icon">💾</span> Save Record
                </button>
            </div>
        </form>
    </div>

    <script>
        // Set today's date on page load
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('input[name="date_issued"]').value = today;
        });

        function cancelForm() {
            if (confirm('Are you sure you want to cancel? All data will be lost.')) {
                document.getElementById('meatTrackingForm').reset();
                const today = new Date().toISOString().split('T')[0];
                document.querySelector('input[name="date_issued"]').value = today;
            }
        }

        function saveRecord() {
            const formData = new FormData(document.getElementById('meatTrackingForm'));
            const data = Object.fromEntries(formData);
            
            // Validate required fields
            if (!data.origin || !data.destination) {
                alert('Please fill in all required fields');
                return;
            }

            console.log('Saving record:', data);
            alert('Record saved successfully!');
            // Here you would send the data to your backend
        }

        function generateQRCode(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('meatTrackingForm'));
            const data = Object.fromEntries(formData);
            
            // Validate required fields
            if (!data.origin || !data.destination) {
                alert('Please fill in all required fields');
                return;
            }

            // Create a string representation of the data
            const qrData = JSON.stringify(data);
            
            console.log('QR Code Data:', qrData);
            alert('QR Code generated and ready to print!');
            // Here you would integrate with a QR code library like QRCode.js
            // and trigger the print dialog
        }

        // Input validation for number fields
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value === '' || isNaN(this.value)) {
                    this.value = '0';
                }
            });
        });
    </script>
</body>
</html>
