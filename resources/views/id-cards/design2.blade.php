<div class="id-card design-2" style="width: 85.6mm; height: 53.98mm; border: 2px solid #c62828; border-radius: 12px; background: white; color: #333; position: relative; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
    <!-- Background Pattern -->
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 30%; background: linear-gradient(135deg, #c62828 0%, #d32f2f 100%); z-index: 1;"></div>

    <!-- Content -->
    <div style="position: relative; z-index: 2; padding: 10px; height: 100%;">
        <!-- School Logo and Title -->
        <div style="text-align: center; margin-bottom: 10px;">
            <h4 style="margin: 0; color: white; font-size: 12px; font-weight: bold;">SCHOOL NAME</h4>
            <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 8px;">OFFICIAL STUDENT ID</p>
        </div>

        <div style="display: flex; height: calc(100% - 50px);">
            <!-- Student Photo -->
            <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                <div style="width: 70px; height: 70px; border: 3px solid white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    <img src="{{ $image_url }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='/uploads/logo/logo.png'">
                </div>
            </div>

            <!-- Student Details -->
            <div style="flex: 2; padding-left: 10px;">
                <div style="background: white; padding: 8px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <div style="margin-bottom: 3px;">
                        <strong style="font-size: 9px; color: #c62828;">STUDENT ID:</strong>
                        <span style="font-size: 9px; font-weight: bold;">{{ $student_id }}</span>
                    </div>
                    <div style="margin-bottom: 3px;">
                        <strong style="font-size: 9px; color: #c62828;">NAME:</strong>
                        <span style="font-size: 9px;">{{ $student_name }}</span>
                    </div>
                    <div style="margin-bottom: 3px;">
                        <strong style="font-size: 8px; color: #c62828;">GRADE:</strong>
                        <span style="font-size: 8px;">{{ $grade }}</span>
                    </div>
                </div>

                <!-- QR Code -->
                <div style="text-align: center; margin-top: 5px;">
                    <div class="qr-code" style="display: inline-block; background: white; padding: 3px; border-radius: 4px;"></div>
                </div>
            </div>
        </div>

        <!-- Valid Date -->
        <div style="position: absolute; bottom: 5px; right: 10px;">
            <p style="margin: 0; font-size: 7px; color: #666;">Valid: {{ $valid_date }}</p>
        </div>
    </div>
</div>