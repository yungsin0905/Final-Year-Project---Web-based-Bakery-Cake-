<?php include_once 'include/config.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['CUSTOMER_ID'])) {
    header("Location: login.php");
    exit();
}

$customer_id = intval($_SESSION['CUSTOMER_ID']);

//reject the custom quoted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'reject'){

  $custom_id  = intval($_POST['custom_id']);

  $custom_query = "SELECT CUSTOM_ID FROM custom
  WHERE CUSTOM_ID = $custom_id AND CUSTOMER_ID = $customer_id AND IS_DELETED = 0";
  $custom_result = mysqli_query($conn, $custom_query);

  if (mysqli_num_rows($custom_result)) {
    // get delivery date
    $row = mysqli_fetch_assoc($custom_result);
    
    $date_query = mysqli_query($conn, "
        SELECT DELIVERY_DATE 
        FROM custom
        WHERE CUSTOM_ID = $custom_id
    ");

    $date_row = mysqli_fetch_assoc($date_query);

    $delivery_date_only = date('Y-m-d', strtotime($date_row['DELIVERY_DATE']));

    // update custom status
    $update_query = "
        UPDATE custom 
        SET STATUS = 'Rejected', REJECTED_BY = 'Customer' 
        WHERE CUSTOM_ID = $custom_id
    ";

    mysqli_query($conn, $update_query);

    // reduce already booked
    mysqli_query($conn, "
        UPDATE production_capacity
        SET ALREADY_BOOKED = ALREADY_BOOKED - 1
        WHERE PRODUCTION_DATE = '$delivery_date_only'
        AND ALREADY_BOOKED > 0
    ");
  }

  header("Location: CustomiseRequest.php");
  exit();
}

//pagination setup
$records_per_page = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $records_per_page;


//count total records for pagination
$count_query = "SELECT COUNT(*) AS total FROM custom
WHERE CUSTOMER_ID = $customer_id AND IS_DELETED = 0";

$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);


$total_records = intval($count_row['total']);

$total_pages = ceil($total_records / $records_per_page);

if ($page > $total_pages && $total_pages > 0) {
  header("Location: CustomiseRequest.php?page=" . $total_pages);
  exit();
}

//fetch paginated records
$sql = "SELECT CUSTOM_ID, STYLE_NAME_SNAPSHOT, CREATED_AT, STATUS, DELIVERY_DATE, QUOTED_PRICE, SIZE, QUANTITY,IDEAL_FLAVOUR, CATER_COUNT,BUDGET,CUSTOM_DES,REF_IMAGE,RECIPIENT_NAME, RECIPIENT_EMAIL, RECIPIENT_PHONE, RECIPIENT_ADDR
FROM custom
WHERE CUSTOMER_ID = $customer_id AND IS_DELETED = 0
ORDER BY CREATED_AT DESC
LIMIT $records_per_page OFFSET $offset";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customise Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="css/header.css?v=3.0">
    <link rel="stylesheet" href="css/footer.css?v=5.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
      :root {
        --main-color: rgb(240, 194, 200);
        --font-color: rgb(101, 54, 31);
        --secondary-color: #fff6e6;
        --rating-color: #c5afb1;
        --search-border-color: rgb(187, 162, 153);
        --bg-color: #fffdf9;
        --font2-color: rgb(147, 103, 82);
      }

      body {
        font-family: 'Quicksand', sans-serif;
        background-color: var(--bg-color);
        margin: 0;
        padding: 0;
      }
      
      /* Back section positioning */
      .back-section {
        display: flex;
        align-items: center;
        margin: 30px 0 20px 50px;
      }

      .back-link {
        text-decoration: none;
        color: var(--font2-color);
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: color 0.3s;
      }

      .back-link:hover {
        text-decoration: underline;
      }

      .page-title {
         color: var(--font-color); 
         font-weight: 700;
         text-align: center;
         margin-bottom: 30px;
      }

       /* Empty state */
      .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--font2-color);
      }
 
      .empty-state i {
        font-size: 60px;
        color: var(--main-color);
        margin-bottom: 15px;
        display: block;
      }
 
      .empty-state p {
        font-size: 16px;
        margin-bottom: 20px;
      }
 
      .btn-make-request {
        background: var(--main-color);
        color: white;
        padding: 10px 25px;
        border-radius: 20px;
        text-decoration: none;
        font-weight: 700;
        transition: 0.3s;
      }
 
      .btn-make-request:hover {
        background: rgb(211, 127, 139);
        color: white;
      }

      /* Compact request card styling using Flexbox */
      .request-card {
        background: white;
        border-radius: 20px;
        padding: 20px 25px;
        margin: 0 auto 20px auto;
        max-width: 1100px;
        box-shadow: 0 8px 24px rgba(147, 103, 82, 0.04);
        border: 1px solid rgba(240, 194, 200, 0.3);
        transition: transform 0.5s, box-shadow 0.2s;
      }

      .request-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(147, 103, 82, 0.08);
      }

      /* Card header row for summary and status badge */
      .request-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px dashed rgba(240, 194, 200, 0.6);
        padding-bottom: 12px;
        margin-bottom: 12px;
      }

      .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 14px;
        color: var(--font2-color);
      }

      .cake-title-badge {
        font-weight: 700;
        color: var(--font-color);
        font-size: 16px;
      }

      .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
      }
      .status-badge.quoted { background: #e1f5fe; color: #0288d1; }
      .status-badge.pending { background: #fff3e0; color: #ef6c00; }
      .status-badge.accepted { background: #e8f5e9; color: #2e7d32; }
      .status-badge.rejected { background: #fce4ec; color: #c62828; }

      /* Main content row positioning price summary alongside action buttons */
      .request-main-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
      }

      .price-summary-box {
        display: flex;
        align-items: flex-end;
        gap: 20px;
      }

      .delivery-summary {
        font-size: 14px;
        color: #666;
        margin-top: 4px;
      }
      .delivery-summary strong {
        color: var(--font-color);
      }

      .price-label {
        font-size: 14px;
        color: var(--font2-color);
        font-weight: 700;
      }

      .price {
        font-size: 14px;
        color: var(--font-color);
        font-weight: 800;
        margin-left: 5px;
      }

      /* Action buttons styles */
      .action-buttons {
        display: flex;
        gap: 10px;
        align-items: center;
      }

      .btn-proceed {
        background: var(--main-color);
        color: white;
        padding: 8px 20px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        transition: 0.5s;
      }

      .btn-reject {
        background: transparent;
        color: #e57373;
        border: 1.5px solid #e57373;
        padding: 7px 18px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        transition: 0.5s;
      }

      .btn-reject:hover { 
        background-color: #e57373; 
        color: white; }

      .btn-proceed:hover { 
        background-color: rgb(211, 127, 139); 
        color: white; 
    }

     /* Status label replacing buttons when not actionable */
      .status-label-accepted {
        font-size: 13px;
        color: #2e7d32;
        font-weight: 600;
      }
      .status-label-rejected {
        font-size: 13px;
        color: #c62828;
        font-weight: 600;
      }

      /* Native master details dropdown custom styling */
      .master-details {
        margin-top: 15px;
        border-top: 1px solid #fcf6f7;
        padding-top: 10px;
      }

      .master-details summary {
        cursor: pointer;
        color: var(--font2-color);
        font-size: 13px;
        font-weight: 600;
        user-select: none;
        outline: none;
        transition: color 0.3s;
      }
      .master-details summary:hover {
        color: var(--font-color);
      }

      /* Grid layout structure for expanded section content */
      .expanded-content {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        padding: 15px 5px 5px 5px;
        animation: fadeIn 0.2s ease-out;
      }

      .expanded-content {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        padding: 15px 5px 5px 5px;
        animation: fadeIn 0.2s ease-out;
      }

      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
      }

      .spec-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--font-color);
        border-left: 3px solid var(--main-color);
        padding-left: 8px;
        margin-bottom: 10px;
      }

      .info-sub-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px 15px;
        font-size: 13.5px;
      }

      .info-sub-grid label, .recipient-info-list label {
        color: var(--font2-color);
        font-weight: 700;
      }

      .recipient-info-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 13.5px;
      }

      .required-image {
        max-height: 160px;
        width: auto;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #eee;
        margin-top: 8px;
      }

      .required-details {
        font-size: 14px;
      }

        /* pagination */
      .pagination{
        display:flex;
        justify-content: center;
        align-items:center;
        gap:15px;
        margin-top:40px;
      }

      .page-btn {
            width: 35px;
            height: 35px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%; 
            background-color: white;
            color: #936752;
            border: 1px solid #936752;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .page-btn.active {
            background-color: #936752;
            color: white;
            border-color: #936752;
        }

        .page-btn:hover:not(.active) {
            background-color: var(--font-color);
            color: white;
        }

        .page-arrow {
            color: #936752;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .page-arrow:hover {
            color: #936752;
        }

         /* Reject confirm modal */
      .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.35);
        z-index: 9999;
        justify-content: center;
        align-items: center;
      }
      .modal-overlay.active {
        display: flex;
      }
      .modal-box {
        background: white;
        border-radius: 20px;
        padding: 35px 30px;
        max-width: 380px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
      }
      .modal-box i {
        font-size: 40px;
        color: #e57373;
        margin-bottom: 15px;
        display: block;
      }
      .modal-box h5 {
        color: var(--font-color);
        font-weight: 700;
        margin-bottom: 10px;
      }
      .modal-box p {
        color: #888;
        font-size: 14px;
        margin-bottom: 25px;
      }
      .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
      }
      .btn-modal-cancel {
        background: #f5f5f5;
        color: #666;
        border: none;
        padding: 9px 22px;
        border-radius: 20px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
      }
      .btn-modal-cancel:hover { background: #e0e0e0; }
      .btn-modal-confirm {
        background: #e57373;
        color: white;
        border: none;
        padding: 9px 22px;
        border-radius: 20px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
      }
      .btn-modal-confirm:hover { background: #c62828; }

      /* Responsive rules for mobile layout breakpoints */
      @media (max-width: 768px) {
        .request-main-row { flex-direction: column; align-items: flex-start; }
        .action-buttons { width: 100%; justify-content: flex-end; }
        .expanded-content { grid-template-columns: 1fr; gap: 20px; }
      }
    </style>
</head>

<body>
    <?php include 'include/header.php'; ?>
    
    <div class="back-section">
        <a href="homepage.php" class="back-link">
            <i class="bi bi-chevron-left"></i>Back
        </a>
    </div>

    <div class="container content-container">
        <h2 class="page-title">My Custom Inquiries</h2>

        <?php if (mysqli_num_rows($result) === 0) :?>
          <!-- Empty state -->
          <div class="empty-state">
                <i class="bi bi-cake2"></i>
                <p>You haven't made any custom cake requests yet.</p>
                <a href="Customise.php" class="btn-make-request">Make a Request</a>
          </div>

          <?php else:?>
            <?php while ($row = mysqli_fetch_assoc($result)):
                $status       = $row['STATUS'];
                $custom_id    = $row['CUSTOM_ID'];
                $style_name   = htmlspecialchars($row['STYLE_NAME_SNAPSHOT'] ?? 'Custom Cake');
                $created_at   = date('d/m/Y', strtotime($row['CREATED_AT']));
                $delivery_dt  = date('d/m/Y', strtotime($row['DELIVERY_DATE']));
                $quoted_price = $row['QUOTED_PRICE'];
                $budget       = number_format($row['BUDGET'], 2);
                $size         = htmlspecialchars($row['SIZE'] ?? '-');
                $flavour      = htmlspecialchars($row['IDEAL_FLAVOUR'] ?? '-');
                $pax          = htmlspecialchars($row['CATER_COUNT']);
                $description  = htmlspecialchars($row['CUSTOM_DES']);
                $ref_image    = htmlspecialchars($row['REF_IMAGE'] ?? '');
                $recip_name   = htmlspecialchars($row['RECIPIENT_NAME']);
                $recip_email  = htmlspecialchars($row['RECIPIENT_EMAIL']);
                $recip_phone  = htmlspecialchars($row['RECIPIENT_PHONE']);
                $recip_addr   = htmlspecialchars($row['RECIPIENT_ADDR']);
                $quantity     = htmlspecialchars($row['QUANTITY']);

                //map status to badge class
                $badge_class = match(strtolower($status)) {
                  'quoted' => 'quoted',
                  'accepted' => 'accepted',
                  'rejected' => 'rejected',
                  default => 'pending'
                };

                //verify if the custom request has been placed
                $paid_query = "SELECT oi.ORDER_ITEM_ID
                FROM order_item oi
                JOIN orders o ON oi.ORDER_ID = o.ORDER_ID
                WHERE oi.CUSTOM_ID = $custom_id
                AND o.ORDER_TYPE = 'Custom'
                LIMIT 1";
                $paid_result = mysqli_query($conn, $paid_query);
                $is_paid = (mysqli_num_rows($paid_result)>0);

              ?>

        <div class="request-card">
            <div class="request-header">
                <div class="header-left">
                    <span class="cake-title-badge"><i class="bi bi-cake2"></i><?= $style_name ?></span>
                    <span class="submit-date text-muted"><i class="bi bi-calendar3"></i> Submitted on: <?= $created_at ?></span>
                </div>
                <span class="status-badge <?= $badge_class ?>"><?= $status ?></span>
            </div>

            <div class="request-main-row">
                <div class="price-summary-box">
                    <div class="delivery-summary">
                        <span>Delivery Date: <strong><?= $delivery_dt ?></strong></span>
                    </div>
                    <div class="final-quote">
                        <span class="price-label">Admin Quote:</span>
                        <?php if ($quoted_price !== null):?>
                        <span class="price">RM <?= number_format($quoted_price, 2); ?></span>
                        <?php else: ?>
                        <span class= "price-pending">Awaiting quote...</span>
                        <?php endif;?>
                    </div>
                </div>
                

                <!-- action button -->
                <div class="action-buttons">

                  <?php if ($status === 'Quoted' && !$is_paid):?>
                    <!-- show reject and proceed only when admin has quoted -->
                    <button class="btn-reject" onclick = "openRejectModal(<?= $custom_id ?>)">Reject</button>
                    <a href="checkout.php?id=<?= $custom_id ?>" class="btn-proceed">Proceed to Checkout</a>

                  <?php elseif ($status === 'Pending'):?>
                    <span class="status-pending text-muted">
                      <i class="bi bi-hourglass-split"></i> Waiting for admin quote...
                    </span>
                  
                  <?php elseif ($status === 'Accepted' && $is_paid):?>
                    <span class="status-accepted ">
                      <i class="bi bi-check-circle"></i> Payment Received. 
                    </span>
                  
                  <?php elseif ($status === 'Rejected'):?>
                    <span class="status-rejected ">
                      <i class="bi bi-x-circle"></i> Quote rejected. You may contact support for more details.
                    </span>
                  <?php endif;?>
                </div>
            </div>
            
            <details class="master-details">
                <summary></i> View My Requirements & Recipient Details</summary>
                
                <div class="expanded-content">
                    <div class="content-col-left">
                        <div class="spec-title">Cake Specifications</div>
                        <div class="info-sub-grid mb-3">
                            <div><label>Cake Size:</label> <span><?= $size ?></span></div>
                            <div><label>Flavor:</label> <span><?= $flavour ?></span></div>
                            <div><label>Pax:</label> <span><?= $pax ?></span></div>
                            <div><label>Budget:</label> <span>RM <?= $budget ?></span></div>
                            <div><label>Quantity:</label> <span><?= $quantity ?></span></div>
                        </div>
                        
                        <div class="required-details">
                            <label class="mt-2 text-muted required-details">Description:</label>
                            <span class="text-muted required-details"> <?= $description ?> </span>
                        </div>

                        <?php if ($ref_image):?>
                        <div class="mt-2">
                            <label class="required-details text-muted">Reference Image:</label>
                            <img class="required-image d-block" src="<?= $ref_image ?>" alt="Reference">
                        </div>
                      <?php endif;?>
                    </div>

                    <!-- recipient details -->
                    <div class="content-col-right">
                        <div class="spec-title">Recipient Details</div>
                        <div class="recipient-info-list">
                            <div><label class="text-muted">Name:</label> <span class="text-muted"><?= $recip_name ?></span></div>
                            <div><label class="text-muted">Email:</label> <span class="text-muted"><?= $recip_email ?></span></div>
                            <div><label class="text-muted">Phone:</label> <span class="text-muted"><?= $recip_phone ?></span></div>
                            <div><label class="text-muted">Delivery Address:</label> <span class="text-muted d-block mt-1 bg-light p-2 rounded" style="font-size:13px;"><?= $recip_addr ?></span></div>
                        </div>
                    </div>
                </div>
            </details>
        </div>
        <?php endwhile; ?>
      <?php endif;?>
    </div>

    <!-- pagination -->
    <?php if ($total_pages > 1) : ?>
      <div class="pagination">
        <?php if($page > 1): ?>
          <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="page-arrow">
            <i class="bi bi-chevron-left"></i>
          </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
            class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>">
            <?php echo $i; ?>
          </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
          <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="page-arrow">
            <i class="bi bi-chevron-right"></i>
          </a>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- COMFIRMATION MODAL -->
     <div class="modal-overlay" id="rejectModal">
        <div class="modal-box">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <h5>Reject this quote?</h5>
            <p>Once rejected, you won't be able to proceed with this order. Are you sure?</p>
            <div class="modal-actions">
                <button class="btn-modal-cancel" onclick="closeRejectModal()">Cancel</button>
                <form method="POST" id="rejectForm">
                    <input type="hidden" name="action"    value="reject">
                    <input type="hidden" name="custom_id" id="rejectCustomId" value="">
                    <button type="submit" class="btn-modal-confirm">Yes, Reject</button>
                </form>
            </div>
        </div>
    </div>

    <?php include 'include/footer.php'; ?>

    <script>
      function openRejectModal(customId) {
            document.getElementById('rejectCustomId').value = customId;
            document.getElementById('rejectModal').classList.add('active');
        }
 
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.remove('active');
        }
 
        // Close modal if clicking outside the box
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) closeRejectModal();
        });
    </script>
</body>
</html>
