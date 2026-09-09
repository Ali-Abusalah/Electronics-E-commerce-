import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import api from '../api/axios'
import { useAuth } from '../context/AuthContext'
import { formatPrice } from '../utils/format'
import { openInvoicePdf } from '../utils/invoice'

export default function MyOrders() {
  const { user } = useAuth()
  const navigate = useNavigate()

  const [orders, setOrders] = useState(null)
  const [error, setError] = useState(null)
  const [downloadingId, setDownloadingId] = useState(null)
  const [downloadError, setDownloadError] = useState(null)

  useEffect(() => {
    if (!user) {
      navigate('/login')
      return
    }

    let active = true
    setOrders(null)
    setError(null)

    api
      .get('/orders')
      .then(({ data }) => {
        if (active) setOrders(data.orders)
      })
      .catch(() => {
        if (active) {
          setError('Could not load your orders. Please try again.')
        }
      })

    return () => {
      active = false
    }
  }, [user, navigate])

  if (!user) return null

  const handleDownload = async (order) => {
    setDownloadingId(order.id)
    setDownloadError(null)
    try {
      await openInvoicePdf(order)
    } catch (err) {
      setDownloadError(`Order ${order.order_number}: ${err.message}`)
    } finally {
      setDownloadingId(null)
    }
  }

  return (
    <div className="orders-page">
      <h1 className="orders-title">My Orders</h1>
      <p className="orders-subtitle">
        Download your tax invoice (PDF with QR code) for any order below.
      </p>

      {error && <div className="error-banner">{error}</div>}
      {downloadError && <div className="error-banner">{downloadError}</div>}

      {orders === null && !error && <p className="orders-loading">Loading your orders…</p>}

      {orders !== null && orders.length === 0 && (
        <div className="orders-empty">
          <div className="empty-icon" aria-hidden="true">🧾</div>
          <h2>No orders yet</h2>
          <p>Once you place an order you'll find its tax invoice here.</p>
          <Link to="/" className="btn btn-primary">Go to Shop</Link>
        </div>
      )}

      {orders !== null && orders.length > 0 && (
        <div className="orders-list">
          {orders.map((order) => (
            <div className="order-card" key={order.id}>
              <div className="order-card-main">
                <p className="order-number">
                  Invoice <strong>{order.invoice_number || order.order_number}</strong>
                  <span className="order-vm">({order.order_number})</span>
                </p>
                <p className="order-meta">
                  {new Date(order.created_at).toLocaleString()} · {order.items_count} item
                  {order.items_count === 1 ? '' : 's'} · {order.status}
                </p>
              </div>
              <div className="order-card-side">
                <strong>{formatPrice(order.total)}</strong>
                <button
                  type="button"
                  className="btn btn-primary btn-sm"
                  onClick={() => handleDownload(order)}
                  disabled={downloadingId === order.id}
                >
                  {downloadingId === order.id
                    ? 'Preparing PDF…'
                    : '⬇ Tax Invoice (PDF)'}
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  )
}
