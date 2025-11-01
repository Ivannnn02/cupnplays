// js/script.js
document.addEventListener('DOMContentLoaded', ()=> {
  const cartBtn = document.getElementById('open-cart-btn');
  const cartPanel = document.getElementById('cart-panel');
  const cartList = document.getElementById('cart-items');
  const cartTotalEl = document.getElementById('cart-total');
  let products = [];
  let cart = [];

  // fetch products
  fetch('../php/get_products.php')
    .then(r=>r.json())
    .then(data=>{
      products = data;
      renderProducts();
    });

  function renderProducts(){
    const container = document.getElementById('menu-grid');
    if(!container) return;
    container.innerHTML = '';
    products.forEach(p=>{
      const disabled = p.stock <= 0 ? 'disabled' : '';
      const el = document.createElement('div');
      el.className = 'product';
      el.innerHTML = `
        <img src="../images/${p.image}" alt="${p.name}" onerror="this.style.opacity=.6;">
        <div class="name">${p.name}</div>
        <div class="price">₱ ${Number(p.price).toFixed(2)}</div>
        <div><small>${p.stock} left</small></div>
        <button ${disabled} data-id="${p.id}">Add</button>
      `;
      container.appendChild(el);
    });

    // attach add buttons
    container.querySelectorAll('button[data-id]').forEach(b=>{
      b.addEventListener('click', (e)=>{
        const id = parseInt(b.getAttribute('data-id'));
        addToCart(id);
      });
    });
  }

  function addToCart(id){
    const product = products.find(p=>p.id==id);
    if(!product || product.stock <= 0) { alert('Out of stock'); return; }
    const inCart = cart.find(c=>c.id==id);
    if(inCart){
      if(inCart.quantity+1 > product.stock) { alert('Not enough stock'); return; }
      inCart.quantity++;
      inCart.subtotal = inCart.quantity * product.price;
    } else {
      cart.push({id:product.id, name:product.name, price:product.price, quantity:1, subtotal:product.price});
    }
    renderCart();
  }

  function renderCart(){
    cartList.innerHTML = '';
    let total = 0;
    cart.forEach(it=>{
      total += it.subtotal;
      const row = document.createElement('div');
      row.className = 'cart-item';
      row.innerHTML = `
        <div class="meta">
          <div style="font-weight:600">${it.name}</div>
          <div>₱ ${Number(it.price).toFixed(2)} · ${it.quantity}pcs</div>
        </div>
        <div>
          <button class="qty-btn" data-op="dec" data-id="${it.id}">-</button>
          <button class="qty-btn" data-op="inc" data-id="${it.id}">+</button>
        </div>
      `;
      cartList.appendChild(row);
    });

    // attach qty buttons
    cartList.querySelectorAll('.qty-btn').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const id = parseInt(btn.getAttribute('data-id'));
        const op = btn.getAttribute('data-op');
        const prod = products.find(p=>p.id==id);
        const c = cart.find(x=>x.id==id);
        if(op==='inc'){
          if(c.quantity+1 > prod.stock) { alert('Not enough stock'); return; }
          c.quantity++; c.subtotal = c.quantity * c.price;
        } else {
          c.quantity--; c.subtotal = c.quantity * c.price;
          if(c.quantity <= 0) cart = cart.filter(x=>x.id!=id);
        }
        renderCart();
      });
    });

    cartTotalEl.innerText = '₱ ' + total.toFixed(2);
  }

  // checkout
  const checkoutBtn = document.getElementById('checkout-btn');
  checkoutBtn && checkoutBtn.addEventListener('click', ()=>{
    if(cart.length === 0) { alert('Cart empty'); return; }
    const payload = { items: cart.map(i => ({ id: i.id, quantity: i.quantity, subtotal: i.subtotal })), total: cart.reduce((s,i)=>s+i.subtotal,0) };
    fetch('../php/save_order.php', {
      method:'POST',
      headers:{ 'Content-Type':'application/json' },
      body: JSON.stringify(payload)
    }).then(r=>r.json()).then(resp=>{
      if(resp.status === 'ok'){
        alert('Order saved: '+resp.order_no);
        cart = [];
        // refresh product list to update stock counts
        return fetch('../php/get_products.php').then(r=>r.json()).then(d=>{
          products = d; renderProducts(); renderCart();
        });
      } else {
        alert('Error: '+(resp.msg || 'Could not save order'));
      }
    }).catch(e=>{
      alert('Request failed');
    });
  });

  // open/close cart panel
  const toggleCartBtns = document.querySelectorAll('[data-toggle="cart"]');
  toggleCartBtns.forEach(b=>b.addEventListener('click', ()=>{
    cartPanel.classList.toggle('open');
    if(cartPanel.classList.contains('open')) cartPanel.style.display = 'block';
    else cartPanel.style.display = 'none';
  }));

  // initial hide
  if(cartPanel) cartPanel.style.display = 'none';
});
