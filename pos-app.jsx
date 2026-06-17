import { useState, useEffect } from "react";

const COLORS = {
  primary: "#1D9E75",
  primaryDark: "#0F6E56",
  primaryLight: "#E1F5EE",
  accent: "#378ADD",
  warning: "#EF9F27",
  danger: "#E24B4A",
  bg: "#f8fafb",
  card: "#ffffff",
  border: "rgba(0,0,0,0.08)",
  text: "#1a1a1a",
  textMuted: "#6b7280",
};

const INITIAL_CATEGORIES = ["Makanan", "Minuman", "Snack", "Lainnya"];

const INITIAL_PRODUCTS = [
  { id: 1, name: "Nasi Goreng Spesial", price: 25000, modal: 15000, category: "Makanan", stock: 50, image: "🍳" },
  { id: 2, name: "Es Teh Manis", price: 5000, modal: 2000, category: "Minuman", stock: 100, image: "🧊" },
  { id: 3, name: "Ayam Bakar", price: 30000, modal: 18000, category: "Makanan", stock: 30, image: "🍗" },
  { id: 4, name: "Keripik Singkong", price: 8000, modal: 4000, category: "Snack", stock: 60, image: "🥔" },
  { id: 5, name: "Jus Alpukat", price: 15000, modal: 8000, category: "Minuman", stock: 40, image: "🥑" },
];

const INITIAL_EMPLOYEES = [
  { id: 1, name: "Budi Santoso", phone: "081234567890", role: "Supervisor", email: "budi@email.com", pin: "1234", access: "supervisor", outlet: 1, photo: "BS" },
  { id: 2, name: "Siti Rahayu", phone: "082345678901", role: "Operator", email: "siti@email.com", pin: "5678", access: "operator", outlet: 1, photo: "SR" },
];

const INITIAL_OUTLETS = [
  { id: 1, name: "Outlet Pusat", phone: "021-1234567", address: "Jl. Sudirman No. 1", kelurahan: "Karet", kodePos: "12920", employees: [1, 2] },
];

const INITIAL_TRANSACTIONS = [
  { id: "TRX001", date: "2025-06-01", items: [{ id: 1, name: "Nasi Goreng Spesial", qty: 2, price: 25000 }, { id: 2, name: "Es Teh Manis", qty: 2, price: 5000 }], total: 60000, paid: 60000, change: 0, cashier: "Siti Rahayu", outlet: "Outlet Pusat" },
  { id: "TRX002", date: "2025-06-02", items: [{ id: 3, name: "Ayam Bakar", qty: 1, price: 30000 }, { id: 5, name: "Jus Alpukat", qty: 1, price: 15000 }], total: 45000, paid: 50000, change: 5000, cashier: "Siti Rahayu", outlet: "Outlet Pusat" },
  { id: "TRX003", date: "2025-06-03", items: [{ id: 4, name: "Keripik Singkong", qty: 3, price: 8000 }], total: 24000, paid: 30000, change: 6000, cashier: "Siti Rahayu", outlet: "Outlet Pusat" },
];

function formatRupiah(n) {
  return "Rp " + Number(n).toLocaleString("id-ID");
}

function Avatar({ initials, color = COLORS.primaryLight, textColor = COLORS.primaryDark, size = 40 }) {
  return (
    <div style={{ width: size, height: size, borderRadius: "50%", background: color, display: "flex", alignItems: "center", justifyContent: "center", fontWeight: 500, fontSize: size * 0.35, color: textColor, flexShrink: 0 }}>
      {initials}
    </div>
  );
}

function Badge({ children, color = "green" }) {
  const map = { green: { bg: COLORS.primaryLight, text: COLORS.primaryDark }, blue: { bg: "#E6F1FB", text: "#185FA5" }, amber: { bg: "#FAEEDA", text: "#854F0B" }, red: { bg: "#FCEBEB", text: "#A32D2D" }, gray: { bg: "#F1EFE8", text: "#5F5E5A" } };
  const c = map[color] || map.green;
  return <span style={{ background: c.bg, color: c.text, borderRadius: 999, padding: "3px 10px", fontSize: 12, fontWeight: 500 }}>{children}</span>;
}

function Card({ children, style = {} }) {
  return <div style={{ background: COLORS.card, borderRadius: 12, border: `1px solid ${COLORS.border}`, ...style }}>{children}</div>;
}

function Modal({ open, onClose, title, children, width = 520 }) {
  if (!open) return null;
  return (
    <div style={{ position: "fixed", inset: 0, background: "rgba(0,0,0,0.4)", zIndex: 100, display: "flex", alignItems: "center", justifyContent: "center", padding: 16 }}>
      <Card style={{ width, maxWidth: "100%", maxHeight: "85vh", overflowY: "auto" }}>
        <div style={{ padding: "20px 24px", borderBottom: `1px solid ${COLORS.border}`, display: "flex", justifyContent: "space-between", alignItems: "center" }}>
          <h3 style={{ margin: 0, fontSize: 16, fontWeight: 600 }}>{title}</h3>
          <button onClick={onClose} style={{ background: "none", border: "none", cursor: "pointer", fontSize: 20, color: COLORS.textMuted, padding: 0 }}>✕</button>
        </div>
        <div style={{ padding: "20px 24px" }}>{children}</div>
      </Card>
    </div>
  );
}

function Input({ label, value, onChange, type = "text", placeholder = "", required = false }) {
  return (
    <div style={{ marginBottom: 16 }}>
      {label && <label style={{ display: "block", marginBottom: 6, fontSize: 14, fontWeight: 500, color: COLORS.text }}>{label}{required && <span style={{ color: COLORS.danger }}> *</span>}</label>}
      <input type={type} value={value} onChange={e => onChange(e.target.value)} placeholder={placeholder}
        style={{ width: "100%", padding: "10px 12px", border: `1px solid ${COLORS.border}`, borderRadius: 8, fontSize: 14, color: COLORS.text, outline: "none", boxSizing: "border-box", background: "#fff" }} />
    </div>
  );
}

function Select({ label, value, onChange, options }) {
  return (
    <div style={{ marginBottom: 16 }}>
      {label && <label style={{ display: "block", marginBottom: 6, fontSize: 14, fontWeight: 500, color: COLORS.text }}>{label}</label>}
      <select value={value} onChange={e => onChange(e.target.value)}
        style={{ width: "100%", padding: "10px 12px", border: `1px solid ${COLORS.border}`, borderRadius: 8, fontSize: 14, color: COLORS.text, outline: "none", background: "#fff" }}>
        {options.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
      </select>
    </div>
  );
}

function PrimaryBtn({ children, onClick, style = {}, danger = false, outline = false }) {
  const base = { padding: "10px 20px", borderRadius: 8, fontSize: 14, fontWeight: 500, cursor: "pointer", border: "none", transition: "opacity .15s" };
  let bg = danger ? COLORS.danger : COLORS.primary;
  let color = "#fff";
  if (outline) { bg = "transparent"; color = danger ? COLORS.danger : COLORS.primary; }
  return <button onClick={onClick} style={{ ...base, background: bg, color, border: outline ? `1px solid ${color}` : "none", ...style }}>{children}</button>;
}

// ===================== PRODUCTS PAGE =====================
function ProductsPage({ products, setProducts }) {
  const [search, setSearch] = useState("");
  const [modal, setModal] = useState(false);
  const [editing, setEditing] = useState(null);
  const [form, setForm] = useState({ name: "", price: "", modal: "", category: "Makanan", stock: "", image: "🛍️" });

  const filtered = products.filter(p => p.name.toLowerCase().includes(search.toLowerCase()));

  function openAdd() { setForm({ name: "", price: "", modal: "", category: "Makanan", stock: "", image: "🛍️" }); setEditing(null); setModal(true); }
  function openEdit(p) { setForm({ name: p.name, price: p.price, modal: p.modal, category: p.category, stock: p.stock, image: p.image }); setEditing(p.id); setModal(true); }
  function save() {
    if (!form.name || !form.price) return;
    const item = { ...form, price: +form.price, modal: +form.modal, stock: +form.stock };
    if (editing) setProducts(ps => ps.map(p => p.id === editing ? { ...p, ...item } : p));
    else setProducts(ps => [...ps, { id: Date.now(), ...item }]);
    setModal(false);
  }
  function del(id) { if (confirm("Hapus produk ini?")) setProducts(ps => ps.filter(p => p.id !== id)); }

  return (
    <div>
      <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: 20 }}>
        <div>
          <h2 style={{ margin: 0, fontSize: 20, fontWeight: 600 }}>Kelola Produk</h2>
          <p style={{ margin: "4px 0 0", color: COLORS.textMuted, fontSize: 14 }}>{products.length} produk terdaftar</p>
        </div>
        <PrimaryBtn onClick={openAdd}>+ Tambah Produk</PrimaryBtn>
      </div>
      <Card style={{ marginBottom: 16 }}>
        <div style={{ padding: "12px 16px" }}>
          <input value={search} onChange={e => setSearch(e.target.value)} placeholder="Cari produk..." style={{ width: "100%", padding: "9px 12px", border: `1px solid ${COLORS.border}`, borderRadius: 8, fontSize: 14, outline: "none", boxSizing: "border-box" }} />
        </div>
      </Card>
      <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(260px, 1fr))", gap: 14 }}>
        {filtered.map(p => (
          <Card key={p.id} style={{ padding: 16 }}>
            <div style={{ display: "flex", alignItems: "flex-start", gap: 12, marginBottom: 12 }}>
              <div style={{ fontSize: 32, width: 48, height: 48, background: COLORS.primaryLight, borderRadius: 10, display: "flex", alignItems: "center", justifyContent: "center" }}>{p.image}</div>
              <div style={{ flex: 1 }}>
                <p style={{ margin: 0, fontWeight: 600, fontSize: 15 }}>{p.name}</p>
                <Badge color="blue">{p.category}</Badge>
              </div>
            </div>
            <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr 1fr", gap: 8, marginBottom: 14, textAlign: "center" }}>
              {[["Harga Jual", formatRupiah(p.price)], ["Harga Modal", formatRupiah(p.modal)], ["Stok", p.stock]].map(([l, v]) => (
                <div key={l} style={{ background: COLORS.bg, borderRadius: 8, padding: "8px 4px" }}>
                  <p style={{ margin: 0, fontSize: 11, color: COLORS.textMuted }}>{l}</p>
                  <p style={{ margin: 0, fontSize: 13, fontWeight: 600 }}>{v}</p>
                </div>
              ))}
            </div>
            <div style={{ display: "flex", gap: 8 }}>
              <PrimaryBtn outline onClick={() => openEdit(p)} style={{ flex: 1, textAlign: "center" }}>Edit</PrimaryBtn>
              <PrimaryBtn danger outline onClick={() => del(p.id)} style={{ flex: 1, textAlign: "center" }}>Hapus</PrimaryBtn>
            </div>
          </Card>
        ))}
      </div>
      <Modal open={modal} onClose={() => setModal(false)} title={editing ? "Edit Produk" : "Tambah Produk"}>
        <Input label="Nama Produk" value={form.name} onChange={v => setForm(f => ({ ...f, name: v }))} required />
        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 12 }}>
          <Input label="Harga Jual" value={form.price} onChange={v => setForm(f => ({ ...f, price: v }))} type="number" />
          <Input label="Harga Modal" value={form.modal} onChange={v => setForm(f => ({ ...f, modal: v }))} type="number" />
        </div>
        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 12 }}>
          <Select label="Kategori" value={form.category} onChange={v => setForm(f => ({ ...f, category: v }))} options={INITIAL_CATEGORIES.map(c => ({ value: c, label: c }))} />
          <Input label="Stok" value={form.stock} onChange={v => setForm(f => ({ ...f, stock: v }))} type="number" />
        </div>
        <Input label="Emoji Produk" value={form.image} onChange={v => setForm(f => ({ ...f, image: v }))} />
        <div style={{ display: "flex", gap: 8, justifyContent: "flex-end" }}>
          <PrimaryBtn outline onClick={() => setModal(false)}>Batal</PrimaryBtn>
          <PrimaryBtn onClick={save}>Simpan</PrimaryBtn>
        </div>
      </Modal>
    </div>
  );
}

// ===================== EMPLOYEES PAGE =====================
function EmployeesPage({ employees, setEmployees, outlets }) {
  const [modal, setModal] = useState(false);
  const [editing, setEditing] = useState(null);
  const [form, setForm] = useState({ name: "", phone: "", role: "Operator", email: "", pin: "", access: "operator", outlet: 1, photo: "" });

  const accessColors = { supervisor: "amber", operator: "green", "non-operator": "gray" };
  const accessLabels = { supervisor: "Supervisor", operator: "Operator", "non-operator": "Non-Operator" };

  function initials(name) { return name.split(" ").map(w => w[0]).join("").slice(0, 2).toUpperCase(); }
  function openAdd() { setForm({ name: "", phone: "", role: "Operator", email: "", pin: "", access: "operator", outlet: outlets[0]?.id || 1, photo: "" }); setEditing(null); setModal(true); }
  function openEdit(e) { setForm({ ...e }); setEditing(e.id); setModal(true); }
  function save() {
    if (!form.name) return;
    const item = { ...form, photo: initials(form.name || "?") };
    if (editing) setEmployees(es => es.map(e => e.id === editing ? { ...e, ...item } : e));
    else setEmployees(es => [...es, { id: Date.now(), ...item }]);
    setModal(false);
  }
  function del(id) { if (confirm("Hapus pegawai ini?")) setEmployees(es => es.filter(e => e.id !== id)); }

  return (
    <div>
      <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: 20 }}>
        <div>
          <h2 style={{ margin: 0, fontSize: 20, fontWeight: 600 }}>Kelola Pegawai</h2>
          <p style={{ margin: "4px 0 0", color: COLORS.textMuted, fontSize: 14 }}>{employees.length} pegawai terdaftar</p>
        </div>
        <PrimaryBtn onClick={openAdd}>+ Tambah Pegawai</PrimaryBtn>
      </div>
      <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(300px, 1fr))", gap: 14 }}>
        {employees.map(emp => (
          <Card key={emp.id} style={{ padding: 16 }}>
            <div style={{ display: "flex", alignItems: "center", gap: 12, marginBottom: 14 }}>
              <Avatar initials={emp.photo || initials(emp.name)} size={48} />
              <div style={{ flex: 1 }}>
                <p style={{ margin: 0, fontWeight: 600, fontSize: 15 }}>{emp.name}</p>
                <div style={{ marginTop: 4 }}><Badge color={accessColors[emp.access] || "gray"}>{accessLabels[emp.access]}</Badge></div>
              </div>
            </div>
            <div style={{ borderTop: `1px solid ${COLORS.border}`, paddingTop: 12 }}>
              {[["📱", emp.phone], ["✉️", emp.email], ["🏪", outlets.find(o => o.id === emp.outlet)?.name || "-"]].map(([icon, val]) => (
                <div key={icon} style={{ display: "flex", gap: 8, marginBottom: 6, fontSize: 13, color: COLORS.textMuted }}>
                  <span>{icon}</span><span>{val}</span>
                </div>
              ))}
            </div>
            <div style={{ display: "flex", gap: 8, marginTop: 12 }}>
              <PrimaryBtn outline onClick={() => openEdit(emp)} style={{ flex: 1, textAlign: "center" }}>Edit</PrimaryBtn>
              <PrimaryBtn danger outline onClick={() => del(emp.id)} style={{ flex: 1, textAlign: "center" }}>Hapus</PrimaryBtn>
            </div>
          </Card>
        ))}
      </div>
      <Modal open={modal} onClose={() => setModal(false)} title={editing ? "Edit Pegawai" : "Tambah Pegawai"}>
        <Input label="Nama Lengkap" value={form.name} onChange={v => setForm(f => ({ ...f, name: v }))} required />
        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 12 }}>
          <Input label="No. Handphone" value={form.phone} onChange={v => setForm(f => ({ ...f, phone: v }))} />
          <Input label="PIN" value={form.pin} onChange={v => setForm(f => ({ ...f, pin: v }))} type="password" />
        </div>
        <Input label="Alamat Email" value={form.email} onChange={v => setForm(f => ({ ...f, email: v }))} type="email" />
        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 12 }}>
          <Select label="Hak Akses" value={form.access} onChange={v => setForm(f => ({ ...f, access: v }))}
            options={[{ value: "supervisor", label: "Supervisor" }, { value: "operator", label: "Operator" }, { value: "non-operator", label: "Non-Operator" }]} />
          <Select label="Outlet" value={form.outlet} onChange={v => setForm(f => ({ ...f, outlet: +v }))}
            options={outlets.map(o => ({ value: o.id, label: o.name }))} />
        </div>
        <div style={{ display: "flex", gap: 8, justifyContent: "flex-end" }}>
          <PrimaryBtn outline onClick={() => setModal(false)}>Batal</PrimaryBtn>
          <PrimaryBtn onClick={save}>Simpan</PrimaryBtn>
        </div>
      </Modal>
    </div>
  );
}

// ===================== OUTLETS PAGE =====================
function OutletsPage({ outlets, setOutlets, employees }) {
  const [modal, setModal] = useState(false);
  const [editing, setEditing] = useState(null);
  const [form, setForm] = useState({ name: "", phone: "", address: "", kelurahan: "", kodePos: "", employees: [] });

  function openAdd() { setForm({ name: "", phone: "", address: "", kelurahan: "", kodePos: "", employees: [] }); setEditing(null); setModal(true); }
  function openEdit(o) { setForm({ ...o }); setEditing(o.id); setModal(true); }
  function save() {
    if (!form.name) return;
    if (editing) setOutlets(os => os.map(o => o.id === editing ? { ...o, ...form } : o));
    else setOutlets(os => [...os, { id: Date.now(), ...form }]);
    setModal(false);
  }
  function del(id) { if (confirm("Hapus outlet ini?")) setOutlets(os => os.filter(o => o.id !== id)); }
  function toggleEmp(id) {
    setForm(f => ({ ...f, employees: f.employees.includes(id) ? f.employees.filter(e => e !== id) : [...f.employees, id] }));
  }

  return (
    <div>
      <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: 20 }}>
        <div>
          <h2 style={{ margin: 0, fontSize: 20, fontWeight: 600 }}>Kelola Outlet</h2>
          <p style={{ margin: "4px 0 0", color: COLORS.textMuted, fontSize: 14 }}>{outlets.length} outlet aktif</p>
        </div>
        <PrimaryBtn onClick={openAdd}>+ Tambah Outlet</PrimaryBtn>
      </div>
      <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(320px, 1fr))", gap: 14 }}>
        {outlets.map(o => (
          <Card key={o.id} style={{ padding: 16 }}>
            <div style={{ display: "flex", gap: 12, alignItems: "flex-start", marginBottom: 14 }}>
              <div style={{ fontSize: 28, width: 48, height: 48, background: "#E6F1FB", borderRadius: 10, display: "flex", alignItems: "center", justifyContent: "center" }}>🏪</div>
              <div>
                <p style={{ margin: 0, fontWeight: 600, fontSize: 15 }}>{o.name}</p>
                <p style={{ margin: "4px 0 0", fontSize: 13, color: COLORS.textMuted }}>{o.phone}</p>
              </div>
            </div>
            <div style={{ background: COLORS.bg, borderRadius: 8, padding: 12, marginBottom: 14, fontSize: 13, color: COLORS.textMuted }}>
              <p style={{ margin: "0 0 4px" }}>📍 {o.address}, {o.kelurahan}</p>
              <p style={{ margin: 0 }}>📮 Kode Pos: {o.kodePos}</p>
            </div>
            <div style={{ marginBottom: 14 }}>
              <p style={{ margin: "0 0 8px", fontSize: 13, fontWeight: 500 }}>Pegawai ({o.employees?.length || 0}):</p>
              <div style={{ display: "flex", flexWrap: "wrap", gap: 6 }}>
                {(o.employees || []).map(eid => {
                  const emp = employees.find(e => e.id === eid);
                  return emp ? <Badge key={eid} color="green">{emp.name}</Badge> : null;
                })}
                {(!o.employees?.length) && <span style={{ fontSize: 13, color: COLORS.textMuted }}>Belum ada pegawai</span>}
              </div>
            </div>
            <div style={{ display: "flex", gap: 8 }}>
              <PrimaryBtn outline onClick={() => openEdit(o)} style={{ flex: 1, textAlign: "center" }}>Edit</PrimaryBtn>
              <PrimaryBtn danger outline onClick={() => del(o.id)} style={{ flex: 1, textAlign: "center" }}>Hapus</PrimaryBtn>
            </div>
          </Card>
        ))}
      </div>
      <Modal open={modal} onClose={() => setModal(false)} title={editing ? "Edit Outlet" : "Tambah Outlet"}>
        <Input label="Nama Outlet" value={form.name} onChange={v => setForm(f => ({ ...f, name: v }))} required />
        <Input label="Nomor Handphone" value={form.phone} onChange={v => setForm(f => ({ ...f, phone: v }))} />
        <Input label="Alamat Outlet" value={form.address} onChange={v => setForm(f => ({ ...f, address: v }))} />
        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 12 }}>
          <Input label="Kelurahan" value={form.kelurahan} onChange={v => setForm(f => ({ ...f, kelurahan: v }))} />
          <Input label="Kode Pos" value={form.kodePos} onChange={v => setForm(f => ({ ...f, kodePos: v }))} />
        </div>
        <div style={{ marginBottom: 16 }}>
          <label style={{ display: "block", marginBottom: 8, fontSize: 14, fontWeight: 500 }}>Pilih Pegawai</label>
          <div style={{ display: "flex", flexDirection: "column", gap: 8, maxHeight: 160, overflowY: "auto" }}>
            {employees.map(e => (
              <label key={e.id} style={{ display: "flex", alignItems: "center", gap: 10, cursor: "pointer", padding: "8px 12px", borderRadius: 8, background: form.employees.includes(e.id) ? COLORS.primaryLight : COLORS.bg }}>
                <input type="checkbox" checked={form.employees.includes(e.id)} onChange={() => toggleEmp(e.id)} style={{ accentColor: COLORS.primary }} />
                <span style={{ fontSize: 14 }}>{e.name}</span>
                <Badge color={e.access === "supervisor" ? "amber" : "green"}>{e.access}</Badge>
              </label>
            ))}
          </div>
        </div>
        <div style={{ display: "flex", gap: 8, justifyContent: "flex-end" }}>
          <PrimaryBtn outline onClick={() => setModal(false)}>Batal</PrimaryBtn>
          <PrimaryBtn onClick={save}>Simpan</PrimaryBtn>
        </div>
      </Modal>
    </div>
  );
}

// ===================== REPORTS PAGE =====================
function ReportsPage({ transactions, products }) {
  const totalRevenue = transactions.reduce((a, t) => a + t.total, 0);
  const totalTransactions = transactions.length;
  const totalItems = transactions.reduce((a, t) => a + t.items.reduce((b, i) => b + i.qty, 0), 0);

  const productSales = {};
  transactions.forEach(t => t.items.forEach(i => {
    if (!productSales[i.name]) productSales[i.name] = { qty: 0, revenue: 0 };
    productSales[i.name].qty += i.qty;
    productSales[i.name].revenue += i.price * i.qty;
  }));
  const topProducts = Object.entries(productSales).sort((a, b) => b[1].revenue - a[1].revenue);
  const maxRevenue = topProducts[0]?.[1]?.revenue || 1;

  return (
    <div>
      <div style={{ marginBottom: 20 }}>
        <h2 style={{ margin: 0, fontSize: 20, fontWeight: 600 }}>Laporan Penjualan</h2>
        <p style={{ margin: "4px 0 0", color: COLORS.textMuted, fontSize: 14 }}>Ringkasan seluruh transaksi</p>
      </div>
      <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 14, marginBottom: 20 }}>
        {[["Total Pendapatan", formatRupiah(totalRevenue), COLORS.primaryLight, COLORS.primaryDark],
          ["Total Transaksi", totalTransactions + " transaksi", "#E6F1FB", "#185FA5"],
          ["Total Item Terjual", totalItems + " item", "#FAEEDA", "#854F0B"]].map(([label, val, bg, color]) => (
          <Card key={label} style={{ padding: 16, background: bg, border: "none" }}>
            <p style={{ margin: "0 0 6px", fontSize: 13, color }}>{label}</p>
            <p style={{ margin: 0, fontSize: 22, fontWeight: 600, color }}>{val}</p>
          </Card>
        ))}
      </div>
      <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 14 }}>
        <Card style={{ padding: 20 }}>
          <h3 style={{ margin: "0 0 16px", fontSize: 15, fontWeight: 600 }}>Produk Terlaris</h3>
          {topProducts.map(([name, data]) => (
            <div key={name} style={{ marginBottom: 12 }}>
              <div style={{ display: "flex", justifyContent: "space-between", marginBottom: 4, fontSize: 13 }}>
                <span style={{ fontWeight: 500 }}>{name}</span>
                <span style={{ color: COLORS.textMuted }}>{data.qty} item</span>
              </div>
              <div style={{ height: 6, background: COLORS.bg, borderRadius: 999, overflow: "hidden" }}>
                <div style={{ height: "100%", width: `${(data.revenue / maxRevenue) * 100}%`, background: COLORS.primary, borderRadius: 999 }} />
              </div>
              <p style={{ margin: "4px 0 0", fontSize: 12, color: COLORS.primaryDark, fontWeight: 500 }}>{formatRupiah(data.revenue)}</p>
            </div>
          ))}
        </Card>
        <Card style={{ padding: 20 }}>
          <h3 style={{ margin: "0 0 16px", fontSize: 15, fontWeight: 600 }}>Riwayat Transaksi</h3>
          <div style={{ display: "flex", flexDirection: "column", gap: 10 }}>
            {transactions.map(t => (
              <div key={t.id} style={{ padding: "10px 12px", background: COLORS.bg, borderRadius: 8 }}>
                <div style={{ display: "flex", justifyContent: "space-between", marginBottom: 4 }}>
                  <span style={{ fontWeight: 600, fontSize: 13 }}>{t.id}</span>
                  <span style={{ fontSize: 13, fontWeight: 600, color: COLORS.primaryDark }}>{formatRupiah(t.total)}</span>
                </div>
                <div style={{ display: "flex", justifyContent: "space-between", fontSize: 12, color: COLORS.textMuted }}>
                  <span>{t.date} · {t.cashier}</span>
                  <span>{t.items.length} item</span>
                </div>
              </div>
            ))}
          </div>
        </Card>
      </div>
    </div>
  );
}

// ===================== TRANSACTION PAGE =====================
function TransactionPage({ products, transactions, setTransactions, outlets, employees }) {
  const [cart, setCart] = useState([]);
  const [search, setSearch] = useState("");
  const [catFilter, setCatFilter] = useState("Semua");
  const [payModal, setPayModal] = useState(false);
  const [paid, setPaid] = useState("");
  const [successModal, setSuccessModal] = useState(null);

  const categories = ["Semua", ...INITIAL_CATEGORIES];
  const filtered = products.filter(p =>
    (catFilter === "Semua" || p.category === catFilter) &&
    p.name.toLowerCase().includes(search.toLowerCase())
  );

  const total = cart.reduce((a, i) => a + i.price * i.qty, 0);
  const change = (+paid || 0) - total;

  function addToCart(p) {
    setCart(c => {
      const ex = c.find(i => i.id === p.id);
      if (ex) return c.map(i => i.id === p.id ? { ...i, qty: i.qty + 1 } : i);
      return [...c, { ...p, qty: 1 }];
    });
  }

  function removeFromCart(id) { setCart(c => c.filter(i => i.id !== id)); }
  function updateQty(id, delta) {
    setCart(c => c.map(i => i.id === id ? { ...i, qty: Math.max(1, i.qty + delta) } : i));
  }

  function processPayment() {
    if (+paid < total) return;
    const trx = {
      id: "TRX" + String(Date.now()).slice(-6),
      date: new Date().toISOString().slice(0, 10),
      items: cart.map(i => ({ id: i.id, name: i.name, qty: i.qty, price: i.price })),
      total,
      paid: +paid,
      change: +paid - total,
      cashier: employees[0]?.name || "Kasir",
      outlet: outlets[0]?.name || "Outlet",
    };
    setTransactions(ts => [trx, ...ts]);
    setSuccessModal(trx);
    setCart([]);
    setPaid("");
    setPayModal(false);
  }

  return (
    <div style={{ display: "grid", gridTemplateColumns: "1fr 340px", gap: 16, height: "calc(100vh - 120px)" }}>
      {/* Products */}
      <div style={{ overflowY: "auto" }}>
        <div style={{ marginBottom: 14 }}>
          <input value={search} onChange={e => setSearch(e.target.value)} placeholder="Cari produk..." style={{ width: "100%", padding: "10px 14px", border: `1px solid ${COLORS.border}`, borderRadius: 10, fontSize: 14, outline: "none", boxSizing: "border-box", marginBottom: 10 }} />
          <div style={{ display: "flex", gap: 8, flexWrap: "wrap" }}>
            {categories.map(c => (
              <button key={c} onClick={() => setCatFilter(c)} style={{ padding: "6px 14px", borderRadius: 999, border: `1px solid ${catFilter === c ? COLORS.primary : COLORS.border}`, background: catFilter === c ? COLORS.primary : "#fff", color: catFilter === c ? "#fff" : COLORS.text, fontSize: 13, cursor: "pointer", fontWeight: 500 }}>{c}</button>
            ))}
          </div>
        </div>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(160px, 1fr))", gap: 10 }}>
          {filtered.map(p => (
            <button key={p.id} onClick={() => addToCart(p)}
              style={{ padding: 14, border: `1px solid ${COLORS.border}`, borderRadius: 10, background: "#fff", cursor: "pointer", textAlign: "left", transition: "all .15s" }}
              onMouseEnter={e => e.currentTarget.style.borderColor = COLORS.primary}
              onMouseLeave={e => e.currentTarget.style.borderColor = COLORS.border}>
              <div style={{ fontSize: 28, marginBottom: 8 }}>{p.image}</div>
              <p style={{ margin: "0 0 4px", fontWeight: 600, fontSize: 13, color: COLORS.text }}>{p.name}</p>
              <p style={{ margin: "0 0 6px", fontSize: 12, color: COLORS.textMuted }}>{p.category}</p>
              <p style={{ margin: 0, fontSize: 13, fontWeight: 700, color: COLORS.primaryDark }}>{formatRupiah(p.price)}</p>
              <div style={{ marginTop: 6, fontSize: 11, color: p.stock < 10 ? COLORS.danger : COLORS.textMuted }}>Stok: {p.stock}</div>
            </button>
          ))}
        </div>
      </div>

      {/* Cart */}
      <Card style={{ display: "flex", flexDirection: "column", overflow: "hidden" }}>
        <div style={{ padding: "16px 18px", borderBottom: `1px solid ${COLORS.border}` }}>
          <h3 style={{ margin: 0, fontSize: 15, fontWeight: 600 }}>Keranjang ({cart.reduce((a, i) => a + i.qty, 0)} item)</h3>
        </div>
        <div style={{ flex: 1, overflowY: "auto", padding: "12px 18px" }}>
          {cart.length === 0 ? (
            <div style={{ textAlign: "center", padding: "40px 0", color: COLORS.textMuted }}>
              <div style={{ fontSize: 40, marginBottom: 10 }}>🛒</div>
              <p style={{ fontSize: 14 }}>Pilih produk untuk ditambahkan</p>
            </div>
          ) : cart.map(item => (
            <div key={item.id} style={{ marginBottom: 12, padding: "10px 12px", background: COLORS.bg, borderRadius: 8 }}>
              <div style={{ display: "flex", justifyContent: "space-between", alignItems: "flex-start", marginBottom: 8 }}>
                <span style={{ fontSize: 13, fontWeight: 600 }}>{item.name}</span>
                <button onClick={() => removeFromCart(item.id)} style={{ background: "none", border: "none", cursor: "pointer", color: COLORS.danger, fontSize: 16, padding: 0 }}>✕</button>
              </div>
              <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                <div style={{ display: "flex", alignItems: "center", gap: 8 }}>
                  <button onClick={() => updateQty(item.id, -1)} style={{ width: 26, height: 26, borderRadius: 6, border: `1px solid ${COLORS.border}`, background: "#fff", cursor: "pointer", fontWeight: 700 }}>−</button>
                  <span style={{ fontSize: 14, fontWeight: 600, minWidth: 20, textAlign: "center" }}>{item.qty}</span>
                  <button onClick={() => updateQty(item.id, 1)} style={{ width: 26, height: 26, borderRadius: 6, border: "none", background: COLORS.primary, color: "#fff", cursor: "pointer", fontWeight: 700 }}>+</button>
                </div>
                <span style={{ fontSize: 13, fontWeight: 600, color: COLORS.primaryDark }}>{formatRupiah(item.price * item.qty)}</span>
              </div>
            </div>
          ))}
        </div>
        <div style={{ padding: "16px 18px", borderTop: `1px solid ${COLORS.border}` }}>
          <div style={{ display: "flex", justifyContent: "space-between", marginBottom: 14 }}>
            <span style={{ fontSize: 15, fontWeight: 500 }}>Total</span>
            <span style={{ fontSize: 18, fontWeight: 700, color: COLORS.primaryDark }}>{formatRupiah(total)}</span>
          </div>
          <button onClick={() => { if (cart.length) setPayModal(true); }}
            disabled={cart.length === 0}
            style={{ width: "100%", padding: "13px", background: cart.length ? COLORS.primary : "#ccc", color: "#fff", border: "none", borderRadius: 10, fontSize: 15, fontWeight: 600, cursor: cart.length ? "pointer" : "default" }}>
            Bayar Sekarang
          </button>
        </div>
      </Card>

      {/* Payment Modal */}
      <Modal open={payModal} onClose={() => setPayModal(false)} title="Proses Pembayaran" width={400}>
        <div style={{ textAlign: "center", padding: "8px 0 20px" }}>
          <p style={{ margin: "0 0 4px", fontSize: 14, color: COLORS.textMuted }}>Total Tagihan</p>
          <p style={{ margin: 0, fontSize: 32, fontWeight: 700, color: COLORS.primaryDark }}>{formatRupiah(total)}</p>
        </div>
        <Input label="Uang Tunai" value={paid} onChange={v => setPaid(v)} type="number" placeholder="Masukkan nominal..." />
        <div style={{ display: "flex", gap: 8, flexWrap: "wrap", marginBottom: 16 }}>
          {[total, Math.ceil(total / 10000) * 10000, Math.ceil(total / 50000) * 50000, Math.ceil(total / 100000) * 100000].filter((v, i, a) => a.indexOf(v) === i).map(v => (
            <button key={v} onClick={() => setPaid(String(v))} style={{ padding: "7px 12px", border: `1px solid ${COLORS.border}`, borderRadius: 8, background: "#fff", cursor: "pointer", fontSize: 13 }}>{formatRupiah(v)}</button>
          ))}
        </div>
        {paid && (
          <div style={{ padding: "12px 16px", background: change >= 0 ? COLORS.primaryLight : "#FCEBEB", borderRadius: 8, marginBottom: 16 }}>
            <div style={{ display: "flex", justifyContent: "space-between" }}>
              <span style={{ fontSize: 14 }}>Kembalian</span>
              <span style={{ fontSize: 16, fontWeight: 700, color: change >= 0 ? COLORS.primaryDark : COLORS.danger }}>{formatRupiah(Math.max(0, change))}</span>
            </div>
          </div>
        )}
        <PrimaryBtn onClick={processPayment} style={{ width: "100%", padding: 13, fontSize: 15 }}>
          Konfirmasi Pembayaran
        </PrimaryBtn>
      </Modal>

      {/* Success Modal */}
      <Modal open={!!successModal} onClose={() => setSuccessModal(null)} title="Transaksi Berhasil" width={400}>
        {successModal && (
          <div style={{ textAlign: "center" }}>
            <div style={{ fontSize: 56, marginBottom: 12 }}>✅</div>
            <p style={{ margin: "0 0 4px", fontSize: 18, fontWeight: 600, color: COLORS.primaryDark }}>{successModal.id}</p>
            <p style={{ margin: "0 0 20px", color: COLORS.textMuted, fontSize: 14 }}>{successModal.date}</p>
            <div style={{ textAlign: "left", background: COLORS.bg, borderRadius: 10, padding: 16, marginBottom: 16 }}>
              {successModal.items.map(i => (
                <div key={i.id} style={{ display: "flex", justifyContent: "space-between", marginBottom: 8, fontSize: 14 }}>
                  <span>{i.name} ×{i.qty}</span>
                  <span>{formatRupiah(i.price * i.qty)}</span>
                </div>
              ))}
              <div style={{ borderTop: `1px solid ${COLORS.border}`, paddingTop: 10, marginTop: 4 }}>
                <div style={{ display: "flex", justifyContent: "space-between", fontWeight: 700 }}><span>Total</span><span>{formatRupiah(successModal.total)}</span></div>
                <div style={{ display: "flex", justifyContent: "space-between", color: COLORS.textMuted, fontSize: 13, marginTop: 4 }}><span>Bayar</span><span>{formatRupiah(successModal.paid)}</span></div>
                <div style={{ display: "flex", justifyContent: "space-between", color: COLORS.primaryDark, fontSize: 13, marginTop: 4, fontWeight: 600 }}><span>Kembalian</span><span>{formatRupiah(successModal.change)}</span></div>
              </div>
            </div>
            <PrimaryBtn onClick={() => setSuccessModal(null)} style={{ width: "100%" }}>Transaksi Baru</PrimaryBtn>
          </div>
        )}
      </Modal>
    </div>
  );
}

// ===================== MAIN APP =====================
export default function POSApp() {
  const [page, setPage] = useState("transaksi");
  const [products, setProducts] = useState(INITIAL_PRODUCTS);
  const [employees, setEmployees] = useState(INITIAL_EMPLOYEES);
  const [outlets, setOutlets] = useState(INITIAL_OUTLETS);
  const [transactions, setTransactions] = useState(INITIAL_TRANSACTIONS);

  const navItems = [
    { id: "produk", icon: "📦", label: "Produk" },
    { id: "pegawai", icon: "👥", label: "Pegawai" },
    { id: "outlet", icon: "🏪", label: "Outlet" },
    { id: "laporan", icon: "📊", label: "Laporan" },
  ];

  return (
    <div style={{ display: "flex", height: "100vh", fontFamily: "system-ui, -apple-system, sans-serif", background: COLORS.bg, color: COLORS.text }}>
      {/* Sidebar */}
      <div style={{ width: 220, background: "#fff", borderRight: `1px solid ${COLORS.border}`, display: "flex", flexDirection: "column" }}>
        <div style={{ padding: "20px 20px 16px" }}>
          <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
            <div style={{ width: 36, height: 36, background: COLORS.primary, borderRadius: 10, display: "flex", alignItems: "center", justifyContent: "center", fontSize: 18 }}>💵</div>
            <div>
              <p style={{ margin: 0, fontWeight: 700, fontSize: 15 }}>KasirPOS</p>
              <p style={{ margin: 0, fontSize: 11, color: COLORS.textMuted }}>Sistem Kasir Online</p>
            </div>
          </div>
        </div>
        <nav style={{ flex: 1, padding: "8px 12px" }}>
          <p style={{ margin: "0 8px 8px", fontSize: 11, fontWeight: 600, color: COLORS.textMuted, textTransform: "uppercase", letterSpacing: 1 }}>Menu</p>
          {navItems.map(item => (
            <button key={item.id} onClick={() => setPage(item.id)}
              style={{ width: "100%", padding: "10px 12px", marginBottom: 2, borderRadius: 8, border: "none", background: page === item.id ? COLORS.primaryLight : "transparent", color: page === item.id ? COLORS.primaryDark : COLORS.text, cursor: "pointer", display: "flex", alignItems: "center", gap: 10, fontSize: 14, fontWeight: page === item.id ? 600 : 400, textAlign: "left" }}>
              <span style={{ fontSize: 16 }}>{item.icon}</span>{item.label}
            </button>
          ))}
        </nav>
        <div style={{ padding: 12 }}>
          <button onClick={() => setPage("transaksi")}
            style={{ width: "100%", padding: "12px", background: COLORS.primary, color: "#fff", border: "none", borderRadius: 10, fontSize: 14, fontWeight: 700, cursor: "pointer", display: "flex", alignItems: "center", justifyContent: "center", gap: 8 }}>
            <span style={{ fontSize: 18 }}>🛒</span> Transaksi
          </button>
        </div>
        <div style={{ padding: "12px 16px", borderTop: `1px solid ${COLORS.border}` }}>
          <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
            <Avatar initials="BS" size={34} />
            <div>
              <p style={{ margin: 0, fontSize: 13, fontWeight: 600 }}>Budi Santoso</p>
              <p style={{ margin: 0, fontSize: 11, color: COLORS.textMuted }}>Supervisor</p>
            </div>
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div style={{ flex: 1, overflowY: "auto", padding: page === "transaksi" ? 16 : 24 }}>
        {page === "produk" && <ProductsPage products={products} setProducts={setProducts} />}
        {page === "pegawai" && <EmployeesPage employees={employees} setEmployees={setEmployees} outlets={outlets} />}
        {page === "outlet" && <OutletsPage outlets={outlets} setOutlets={setOutlets} employees={employees} />}
        {page === "laporan" && <ReportsPage transactions={transactions} products={products} />}
        {page === "transaksi" && <TransactionPage products={products} transactions={transactions} setTransactions={setTransactions} outlets={outlets} employees={employees} />}
      </div>
    </div>
  );
}
