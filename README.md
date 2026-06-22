1.ໂຄງສ້າງຖານຂໍ້ມູນ (SQL Schema)
luangprabang_heritage.sql (ຕັ້ງຢູ່ໃນໂຟນເດີຫຼັກຂອງໂຄງການ)
ນີ້ແມ່ນໄຟລ໌ SQL dump ທີ່ມີໂຄງສ້າງຕາຕະລາງ (ເຊັ່ນ: heritage_houses) ແລະຂໍ້ມູນເບື້ອງຕົ້ນສຳລັບການນຳເຂົ້າໃນຖານຂໍ້ມູນ MySQL/MariaDB.

2. ການຕັ້ງຄ່າການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
config/database.php 
ນີ້ແມ່ນໄຟລ໌ PHP ທີ່ໃຊ້ເພື່ອເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນ MySQL. ມັນດຶງເອົາຄ່າຈາກຕົວແປສະພາບແວດລ້ອມ ຫຼື ໃຊ້ຄ່າເລີ່ມຕົ້ນຕໍ່ໄປນີ້:
ຊື່ຖານຂໍ້ມູນ: luangprabang_heritage
Host: 127.0.0.1 (Localhost)
Port: 3307
ຜູ້ໃຊ້: root

config/connect.json
ໄຟລ໌ນີ້ເກັບຮັກສາຂໍ້ມູນການຕັ້ງຄ່າສຳລັບການເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນ ເຊັ່ນ: Railway,Host, User, Password, ແລະ Port 3306.
