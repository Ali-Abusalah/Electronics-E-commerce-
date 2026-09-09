import { useEffect, useMemo, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import api from '../api/axios'
import { useAuth } from '../context/AuthContext'
import { useCart } from '../context/CartContext'
import { formatPrice } from '../utils/format'
import { openInvoicePdf } from '../utils/invoice'

const DELIVERY_FEES = { standard: 4.99, express: 12.99 }
// Sales tax (VAT) percentage charged on every item — must match the backend
// config/invoice.php tax rate.
const VAT_RATE = 16

function round2(value) {
  return Math.round(value * 100) / 100
}

// VAT charged on every item line, rounded per line exactly like the backend:
// vat_per_line = round2(price × qty × 16 / 100), then summed.
function computeVat(items) {
  return items.reduce(
    (sum, item) => sum + round2(item.product.price * item.quantity * VAT_RATE / 100),
    0
  )
}

export default function Checkout() {
  const { user } = useAuth()
  const { items, cartTotal, clearCart } = useCart()
  const navigate = useNavigate()

  const [form, setForm] = useState({
    name: user?.name || '',
    phone: '',
    address: '',
    city: '',
    delivery_method: 'standard',
    payment_method: 'cod',
    // Card details (not sent to server — placeholder UI)
    card_number: '',
    card_expiry: '',
    card_cvv: '',
  })
  const [errors, setErrors] = useState({})
  const [successOrder, setSuccessOrder] = useState(null)
  const [submitting, setSubmitting] = useState(false)
  const [downloadingInvoice, setDownloadingInvoice] = useState(false)
  const [invoiceError, setInvoiceError] = useState(null)
  // Payment flow: 1 = choose method, 2 = card details, 3 = confirmation
  const [paymentStep, setPaymentStep] = useState(1)

  const subtotal = useMemo(() => cartTotal(), [cartTotal])

  const vatAmount = useMemo(() => computeVat(items), [items])

  const deliveryFee = useMemo(() => {
    if (form.delivery_method === 'express') return DELIVERY_FEES.express
    return subtotal >= 50 ? 0 : DELIVERY_FEES.standard
  }, [form.delivery_method, subtotal])

  const total = round2(subtotal + vatAmount + deliveryFee)

  useEffect(() => {
    if (!user) {
      navigate('/login')
    }
  }, [user, navigate])

  if (!user) return null

  const handleDownloadInvoice = async () => {
    if (!successOrder) return
    setDownloadingInvoice(true)
    setInvoiceError(null)
    try {
      await openInvoicePdf(successOrder)
    } catch (err) {
      setInvoiceError(err.message)
    } finally {
      setDownloadingInvoice(false)
    }
  }

  const handleChange = (field) => (e) =>
    setForm({ ...form, [field]: e.target.value })

  const handleCardChange = (field) => (e) =>
    setForm({ ...form, [field]: e.target.value })

  // Format card number with spaces: 4111 1111 1111 1111
  const handleCardNumberChange = (e) => {
    let val = e.target.value.replace(/[^0-9]/g, '').slice(0, 16)
    const formatted = val.match(/.{1,4}/g)?.join(' ') || val
    setForm({ ...form, card_number: formatted })
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setSubmitting(true)
    setErrors({})

    const payload = {
      customer_name: form.name,
      customer_email: user?.email || null,
      customer_phone: form.phone,
      shipping_address: form.address,
      city: form.city,
      delivery_method: form.delivery_method,
      payment_method: form.payment_method,
      items: items.map((item) => ({
        product_id: item.product.id,
        quantity: item.quantity,
      })),
    }

    try {
      const { data } = await api.post('/orders', payload)
      setSuccessOrder(data)
      clearCart()
    } catch (err) {
      const data = err.response?.data
      if (data?.errors) {
        const flat = {}
        Object.entries(data.errors).forEach(([key, msgs]) => {
          flat[key] = msgs[0]
        })
        setErrors(flat)
      } else {
        setErrors({ form: data?.message || 'Failed to place order. Please try again.' })
      }
    } finally {
      setSubmitting(false)
    }
  }

  if (successOrder) {
    return (
      <div className="checkout-success">
        <div className="success-icon" aria-hidden="true">✓</div>
        <h1>Order Confirmed!</h1>
        <p>Order number: <strong>{successOrder.order_number}</strong></p>
        <p>
          Subtotal: <strong>{formatPrice(successOrder.subtotal)}</strong>
        </p>
        <p>
          VAT ({VAT_RATE}%):{' '}
          <strong>{formatPrice(successOrder.tax_amount ?? 0)}</strong>
        </p>
        <p>
          Total (incl. VAT): <strong>{formatPrice(successOrder.total)}</strong>
        </p>
        <p>
          Your order will be delivered to {successOrder.shipping_address},{' '}
          {successOrder.city}.
        </p>
        {invoiceError && (
          <span className="field-error" style={{ display: 'block' }}>{invoiceError}</span>
        )}
        <button
          type="button"
          className="btn btn-primary"
          onClick={handleDownloadInvoice}
          disabled={downloadingInvoice}
        >
          {downloadingInvoice ? 'Preparing PDF...' : '⬇ Download Tax Invoice (PDF)'}
        </button>
        <button
          type="button"
          className="btn btn-ghost"
          onClick={() => navigate('/')}
        >
          Continue Shopping
        </button>
      </div>
    )
  }

  if (items.length === 0) {
    return (
      <div className="checkout-empty">
        <div className="empty-icon" aria-hidden="true">🛒</div>
        <h1>Your cart is empty</h1>
        <p>Add some products before checking out.</p>
        <Link to="/" className="btn btn-primary">Go to Shop</Link>
      </div>
    )
  }

  // ── Payment UI helpers ────────────────────────────────────────────
  const renderPaymentStep = () => {
    if (form.payment_method === 'cod') {
      // COD: show confirmation directly
      return (
        <div className="payment-step">
          <div className="payment-step-card">
            <div className="payment-step-badge">2</div>
            <h3>Cash on Delivery</h3>
            <p>You'll pay <strong>{formatPrice(total)}</strong> in cash when your order arrives.</p>
            <p className="text-muted" style={{ fontSize: '13px' }}>
              No online payment needed — just bring cash on delivery day.
            </p>
            <button
              type="button"
              className="btn btn-primary btn-block"
              disabled={submitting}
              onClick={handleSubmit}
            >
              {submitting ? 'Placing Order...' : `Confirm · ${formatPrice(total)}`}
            </button>
          </div>
        </div>
      )
    }

    // Card payment: 3 steps
    if (paymentStep === 1) {
      return (
        <div className="payment-step">
          <div className="payment-step-cards">
            <label className={`option-card ${form.payment_method === 'cod' ? 'selected' : ''}`}>
              <input
                type="radio"
                name="payment"
                value="cod"
                checked={form.payment_method === 'cod'}
                onChange={() => { setForm({ ...form, payment_method: 'cod' }); setPaymentStep(2) }}
              />
              <div>
                <strong>Cash on Delivery</strong>
                <span>Pay when your order arrives</span>
              </div>
            </label>
            <label className={`option-card ${form.payment_method === 'card' ? 'selected' : ''}`}>
              <input
                type="radio"
                name="payment"
                value="card"
                checked={form.payment_method === 'card'}
                onChange={() => { setForm({ ...form, payment_method: 'card' }); setPaymentStep(2) }}
              />
              <div>
                <strong>✨ Card Payment</strong>
                <span>Credit / debit card — secure & fast</span>
              </div>
            </label>
          </div>
          {paymentStep === 1 && (
            <button
              type="button"
              className="btn btn-primary btn-block"
              disabled={!form.payment_method}
              onClick={() => setPaymentStep(2)}
            >
              Continue
            </button>
          )}
        </div>
      )
    }

    if (paymentStep === 2) {
      // Card details step
      return (
        <div className="payment-step">
          {form.payment_method === 'card' && (
            <div className="payment-step-card">
              <div className="payment-step-badge">2</div>
              <h3>Card Details</h3>
              <p className="text-muted" style={{ fontSize: '13px', marginBottom: '16px' }}>
                Enter your card information to charge {formatPrice(total)} on delivery.
              </p>

              <div className="form-field">
                <label htmlFor="card_number">Card number</label>
                <input
                  id="card_number"
                  type="text"
                  inputMode="numeric"
                  maxLength={19}
                  value={form.card_number}
                  onChange={handleCardNumberChange}
                  placeholder="4111 1111 1111 1111"
                  required
                />
                {errors.card_number && (
                  <span className="field-error">{errors.card_number}</span>
                )}
              </div>

              <div className="card-row">
                <div className="form-field">
                  <label htmlFor="card_expiry">Expiry (MM/YY)</label>
                  <input
                    id="card_expiry"
                    type="text"
                    inputMode="numeric"
                    maxLength={5}
                    value={form.card_expiry}
                    onChange={(e) => {
                      let v = e.target.value.replace(/[^0-9]/g, '').slice(0, 4)
                      if (v.length >= 2) v = v.slice(0, 2) + '/' + v.slice(2)
                      setForm({ ...form, card_expiry: v })
                    }}
                    placeholder="12/26"
                    required
                  />
                  {errors.card_expiry && (
                    <span className="field-error">{errors.card_expiry}</span>
                  )}
                </div>

                <div className="form-field">
                  <label htmlFor="card_cvv">CVV</label>
                  <input
                    id="card_cvv"
                    type="password"
                    inputMode="numeric"
                    maxLength={4}
                    value={form.card_cvv}
                    onChange={(e) => {
                      setForm({ ...form, card_cvv: e.target.value.replace(/[^0-9]/g, '').slice(0, 4) })
                    }}
                    placeholder="123"
                    required
                  />
                  {errors.card_cvv && (
                    <span className="field-error">{errors.card_cvv}</span>
                  )}
                </div>
              </div>

              <div className="payment-nav">
                <button
                  type="button"
                  className="btn btn-ghost"
                  onClick={() => setPaymentStep(1)}
                >
                  ← Back
                </button>
                <button
                  type="button"
                  className="btn btn-primary"
                  disabled={!form.card_number || !form.card_expiry || !form.card_cvv}
                  onClick={() => setPaymentStep(3)}
                >
                  Review Payment
                </button>
              </div>
            </div>
          )}
        </div>
      )
    }

    // Step 3: Confirmation
    return (
      <div className="payment-step">
        <div className="payment-step-card confirm">
          <div className="payment-step-badge">3</div>
          <h3>Confirm Payment</h3>
          <p className="text-muted" style={{ fontSize: '13px', marginBottom: '16px' }}>
            Please review before confirming. Your card will be charged when the order is placed.
          </p>

          <div className="payment-confirm-detail">
            <div className="detail-line">
              <span>Payment method</span>
              <strong>{form.payment_method === 'cod' ? 'Cash on Delivery' : 'Card Payment'}</strong>
            </div>
            {form.payment_method === 'card' && (
              <div className="detail-line">
                <span>Card</span>
                <strong>
                  {form.card_number
                    ? `•••• ${form.card_number.split(' ').pop()}`
                    : '—'}
                </strong>
              </div>
            )}
            <div className="detail-line">
              <span>Amount</span>
              <strong>{formatPrice(total)}</strong>
            </div>
          </div>

          <div className="payment-confirm-total">
            <span>Total to pay</span>
            <strong>{formatPrice(total)}</strong>
          </div>

          <div className="payment-nav">
            <button
              type="button"
              className="btn btn-ghost"
              onClick={() => setPaymentStep(2)}
            >
              ← Edit
            </button>
            <button
              type="button"
              className="btn btn-primary btn-block"
              disabled={submitting}
              onClick={handleSubmit}
            >
              {submitting ? 'Placing Order...' : `Pay {formatPrice(total)} · Confirm`}
            </button>
          </div>
        </div>
      </div>
    )
  }

  return (
    <div className="checkout-page">
      <h1 className="checkout-title">Checkout</h1>

      {errors.form && <div className="error-banner">{errors.form}</div>}

      <div className="checkout-grid">
        <form className="checkout-form" onSubmit={handleSubmit} noValidate>
          <section className="checkout-section">
            <h2>Shipping Details</h2>

            <div className="form-field">
              <label htmlFor="name">Full name</label>
              <input
                id="name"
                type="text"
                value={form.name}
                onChange={handleChange('name')}
                required
              />
              {errors.customer_name && (
                <span className="field-error">{errors.customer_name}</span>
              )}
            </div>

            <div className="form-field">
              <label htmlFor="phone">Phone</label>
              <input
                id="phone"
                type="tel"
                value={form.phone}
                onChange={handleChange('phone')}
                required
              />
              {errors.customer_phone && (
                <span className="field-error">{errors.customer_phone}</span>
              )}
            </div>

            <div className="form-field">
              <label htmlFor="address">Shipping address</label>
              <textarea
                id="address"
                rows="3"
                value={form.address}
                onChange={handleChange('address')}
                required
              />
              {errors.shipping_address && (
                <span className="field-error">{errors.shipping_address}</span>
              )}
            </div>

            <div className="form-field">
              <label htmlFor="city">City</label>
              <input
                id="city"
                type="text"
                value={form.city}
                onChange={handleChange('city')}
                required
              />
              {errors.city && <span className="field-error">{errors.city}</span>}
            </div>
          </section>

          <section className="checkout-section">
            <h2>Delivery Method</h2>
            <div className="option-row">
              <label className={`option-card ${form.delivery_method === 'standard' ? 'selected' : ''}`}>
                <input
                  type="radio"
                  name="delivery"
                  value="standard"
                  checked={form.delivery_method === 'standard'}
                  onChange={handleChange('delivery_method')}
                />
                <div>
                  <strong>Standard Delivery</strong>
                  <span>{subtotal >= 50 ? 'Free' : formatPrice(4.99)} · 3–5 business days</span>
                </div>
              </label>
              <label className={`option-card ${form.delivery_method === 'express' ? 'selected' : ''}`}>
                <input
                  type="radio"
                  name="delivery"
                  value="express"
                  checked={form.delivery_method === 'express'}
                  onChange={handleChange('delivery_method')}
                />
                <div>
                  <strong>Express Delivery</strong>
                  <span>{formatPrice(12.99)} · 1–2 business days</span>
                </div>
              </label>
            </div>
          </section>

          {/* ── Centered payment section ──────────────────────── */}
          <section className="checkout-section payment-section">
            <h2 className="payment-heading">Payment</h2>
            {renderPaymentStep()}
          </section>

          {/* Hidden submit kept for backwards compat — actual submission
             is done from within the payment steps. */}
          <button
            type="submit"
            className="btn btn-primary btn-block"
            style={{ display: 'none' }}
            disabled={submitting}
          >
            {submitting ? 'Placing Order...' : `Place Order · ${formatPrice(total)}`}
          </button>
        </form>

        <aside className="checkout-summary">
          <h2>Order Summary</h2>
          <ul className="summary-items">
            {items.map((item) => (
              <li key={item.product.id}>
                <span className="summary-qty">×{item.quantity}</span>
                <span className="summary-name">{item.product.name}</span>
                <span className="summary-price">
                  {formatPrice(item.product.price * item.quantity)}
                </span>
              </li>
            ))}
          </ul>
          <div className="summary-totals">
            <div className="summary-row">
              <span>Subtotal</span>
              <span>{formatPrice(subtotal)}</span>
            </div>
            <div className="summary-row">
              <span>VAT ({VAT_RATE}%)</span>
              <span>{formatPrice(vatAmount)}</span>
            </div>
            <div className="summary-row">
              <span>Delivery</span>
              <span>{deliveryFee === 0 ? 'Free' : formatPrice(deliveryFee)}</span>
            </div>
            <div className="summary-row total">
              <span>Total (incl. VAT)</span>
              <span>{formatPrice(total)}</span>
            </div>
          </div>
        </aside>
      </div>
    </div>
  )
}