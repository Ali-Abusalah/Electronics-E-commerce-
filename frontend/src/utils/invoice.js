import api from '../api/axios'

/**
 * Fetch an order's tax invoice PDF and open it in a new tab so the customer
 * can print or save it. Throws an Error with a friendly server message when
 * the invoice cannot be issued (e.g. order has no purchases).
 */
export async function openInvoicePdf(order) {
  let res
  try {
    res = await api.get(`/orders/${order.id}/invoice`, {
      responseType: 'blob',
    })
  } catch (err) {
    let message = 'Could not download the invoice. Please try again.'
    try {
      if (err.response?.data instanceof Blob) {
        const parsed = JSON.parse(await err.response.data.text())
        if (parsed?.message) message = parsed.message
      } else if (err.response?.data?.message) {
        message = err.response.data.message
      }
    } catch {
      // keep the generic message
    }
    throw new Error(message)
  }

  const url = URL.createObjectURL(res.data)
  const win = window.open(url, '_blank')

  if (!win) {
    // Popup blocked — fall back to a direct download link.
    const a = document.createElement('a')
    a.href = url
    a.download = `invoice-${order.invoice_number || order.order_number}.pdf`
    document.body.appendChild(a)
    a.click()
    a.remove()
  }

  setTimeout(() => URL.revokeObjectURL(url), 120000)
}
