<div class="container_khachhang">
    <h1>Danh Sách Khách Hàng</h1>

    <div class="filter-search-wrapper">
        <div class="filter-container">
            <select id="statusFilter" onchange="filterByStatus()">
                <option value="" disabled selected style="display:none;">Trạng thái</option>
                <option value="theodoitaikhoan">Tất cả</option>
                <option value="tkhoatdong">Hoạt Động</option>
                <option value="tkbikhoa">Bị Khóa</option>
            </select>
        </div>

        <!-- Search form bên góc phải -->
        <div class="search-container">
            <form action="" method="GET">
                <input type="hidden" name="action" value="quanlytaikhoan">
                <input type="hidden" name="query" value="theodoitaikhoan">
                <input type="text" name="search" placeholder="Tìm kiếm theo tên..." id="searchInput">
                <button type="submit" id="searchButton">Tìm kiếm</button>
            </form>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Khách Hàng</th>
                <th>Tên Khách Hàng</th>
                <th>Ngày Sinh</th>
                <th>Số Điện Thoại</th>
                <th>Email</th>
                <th>Trạng Thái Tài Khoản</th>
                <th>Loại Khách Hàng</th>
                <th>Thao Tác</th>
                <th>Quản Lý Tài Khoản</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'connect/mysqlconnect.php';

            // Thiết lập giá trị mặc định là 'tkbikhoa' nếu không có lựa chọn trạng thái
            $trangthai = isset($_GET['status']) ? $_GET['status'] : 'tkbikhoa';

            // Truy vấn để lấy thông tin khách hàng với trạng thái cụ thể
            $sql = "SELECT T.idkh, T.Tenkh, T.Ngaysinh, T.SDT, T.Gmail, Q.Trangthai, Q.Loaikh 
                    FROM ThongTinKhachHang T
                    JOIN QuanLyTaiKhoan Q ON T.idkh = Q.idkh";
            
            if ($trangthai === 'tkhoatdong') {
                $sql .= " WHERE Q.Trangthai = 'hoatdong'";
            } elseif ($trangthai === 'tkbikhoa') {
                $sql .= " WHERE Q.Trangthai = 'bikhoa'";
            }

            $result = $mysqli->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["idkh"] . "</td>";
                    echo "<td>" . $row["Tenkh"] . "</td>";
                    echo "<td>" . ($row["Ngaysinh"] ? date("Y-m-d", strtotime($row["Ngaysinh"])) : 'N/A') . "</td>";
                    echo "<td>" . $row["SDT"] . "</td>";
                    echo "<td>" . $row["Gmail"] . "</td>";

                    $trangThai = $row["Trangthai"] === 'hoatdong' ? '<span class="active-status">hoạt động</span>' : '<span class="inactive-status">Bị khóa</span>';
                    echo "<td>" . $trangThai . "</td>";

                    $loaiKhachHang = $row["Loaikh"] === 'khachVIP' ? '<span class="vip-customer">KHÁCH VIP</span>' : 'Khách hàng thường';
                    echo "<td>" . $loaiKhachHang . "</td>";

                    echo "<td>
                            <form action='?action=quanlytaikhoan&query=suattkhachhang' method='POST' style='display:inline;'>
                                <input type='hidden' name='id' value='" . $row["idkh"] . "'>
                                <button type='submit' id='edit_button' style='background: none; border: none; padding: 0;'>
                                    <i class='fas fa-edit' title='Sửa'></i>
                                </button>
                            </form>
                          </td>";
                    
                    echo "<td>";
                    
                    if ($row["Trangthai"] === 'hoatdong') {
                        echo "<button type='button' id='cus_activate' style='background: none; border: none; padding: 0; color: grey;' disabled>
                                <i class='fas fa-check' title='Kích Hoạt'></i>
                              </button>";
                        echo "<form action='pages/quanlytaikhoan/khoatk.php' method='POST' style='display:inline;' onsubmit='return confirmDeactivation()'>
                                <input type='hidden' name='id' value='" . $row["idkh"] . "'>
                                <button type='submit' id='cus_lock' style='background: none; border: none; padding: 0; color: red;'>
                                    <i class='fas fa-ban' title='Khóa'></i>
                                </button>
                              </form>";
                    } else {
                        echo "<form action='pages/quanlytaikhoan/motk.php' method='POST' style='display:inline;' onsubmit='return confirmActivation()'>
                                <input type='hidden' name='id' value='" . $row["idkh"] . "'>
                                <button type='submit' id='cus_activate' style='background: none; border: none; padding: 0; color: green;'>
                                    <i class='fas fa-check' title='Kích Hoạt'></i>
                                </button>
                              </form>";
                        echo "<button type='button' id='cus_lock' style='background: none; border: none; padding: 0; color: grey;' disabled>
                                <i class='fas fa-ban' title='Khóa'></i>
                              </button>";
                    }

                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Không có dữ liệu để hiển thị</td></tr>";
            }

            $mysqli->close();
            ?>
        </tbody>
    </table>
</div>

<script>
    function confirmActivation() {
        return confirm("Bạn có chắc chắn muốn kích hoạt tài khoản này không?");
    }

    function confirmDeactivation() {
        return confirm("Bạn có chắc chắn muốn khóa tài khoản này không?");
    }

    function filterByStatus() {
        const filter = document.getElementById('statusFilter').value;
        window.location.href = window.location.pathname + "?action=quanlytaikhoan&query=" + filter;
    }
</script>
