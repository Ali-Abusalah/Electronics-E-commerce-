import { useEffect, useState } from 'react'
import api from '../api/axios'
import { useAuth } from '../context/AuthContext'

export default function Reviews({ productId }) {
  const { user } = useAuth()
  const [reviews, setReviews] = useState([])
  const [rating, setRating] = useState(5)
  const [comment, setComment] = useState('')
  const [loading, setLoading] = useState(true)
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    api.get(`/products/${productId}/reviews`)
      .then(({ data }) => setReviews(data))
      .catch(() => {})
      .finally(() => setLoading(false))
  }, [productId])

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!comment.trim()) return
    setSubmitting(true)
    try {
      const { data } = await api.post(`/products/${productId}/reviews`, { rating, comment })
      setReviews((prev) => {
        const exists = prev.find((r) => r.user_id === data.user_id)
        if (exists) {
          return prev.map((r) => (r.user_id === data.user_id ? data : r))
        }
        return [data, ...prev]
      })
      setComment('')
      setRating(5)
    } catch {
    } finally {
      setSubmitting(false)
    }
  }

  const avg = reviews.length ? (reviews.reduce((s, r) => s + r.rating, 0) / reviews.length).toFixed(1) : '0.0'

  if (loading) return null

  return (
    <div className="reviews-section">
      <div className="reviews-header">
        <h3>Customer Reviews</h3>
        <div className="reviews-summary">
          <span className="stars">★</span>
          <span>{avg} out of 5</span>
          <span>({reviews.length} {reviews.length === 1 ? 'review' : 'reviews'})</span>
        </div>
      </div>

      {reviews.map((review) => (
        <div key={review.id} className="review-card">
          <div className="review-card-header">
            <span className="review-author">{review.user?.name || 'Anonymous'}</span>
            <span className="review-date">
              {new Date(review.created_at).toLocaleDateString()}
            </span>
          </div>
          <div className="review-stars">{'★'.repeat(review.rating)}{'☆'.repeat(5 - review.rating)}</div>
          {review.comment && <p className="review-text">{review.comment}</p>}
        </div>
      ))}

      {reviews.length === 0 && !user && (
        <p className="no-reviews">No reviews yet. Be the first to review this product!</p>
      )}

      {user && (
        <form className="review-form" onSubmit={handleSubmit}>
          <h4>Write a Review</h4>
          <div className="star-input">
            {[1, 2, 3, 4, 5].map((s) => (
              <button
                key={s}
                type="button"
                className={s <= rating ? 'active' : ''}
                onClick={() => setRating(s)}
                aria-label={`${s} star${s > 1 ? 's' : ''}`}
              >
                ★
              </button>
            ))}
          </div>
          <textarea
            placeholder="Share your experience with this product..."
            value={comment}
            onChange={(e) => setComment(e.target.value)}
            maxLength={1000}
          />
          <button type="submit" className="btn btn-primary" disabled={submitting || !comment.trim()}>
            {submitting ? 'Submitting...' : 'Submit Review'}
          </button>
        </form>
      )}

      {!user && reviews.length > 0 && (
        <p className="no-reviews">Log in to leave a review.</p>
      )}
    </div>
  )
}
