import { useEffect, useMemo, useState } from 'react';

const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8080/api/notes';

const emptyForm = { title: '', content: '' };

export default function App() {
  const [notes, setNotes] = useState([]);
  const [form, setForm] = useState(emptyForm);
  const [editingId, setEditingId] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const isEditing = useMemo(() => editingId !== null, [editingId]);

  async function fetchNotes() {
    setLoading(true);
    setError('');
    try {
      const response = await fetch(API_BASE);
      const data = await response.json();
      if (!response.ok) {
        throw new Error(data.message || 'Failed to load notes');
      }
      setNotes(data);
    } catch (e) {
      setError(e.message);
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    fetchNotes();
  }, []);

  function onChange(event) {
    const { name, value } = event.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  }

  async function onSubmit(event) {
    event.preventDefault();
    setError('');

    const method = isEditing ? 'PUT' : 'POST';
    const url = isEditing ? `${API_BASE}/${editingId}` : API_BASE;

    try {
      const response = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(form),
      });

      if (!response.ok) {
        const data = await response.json();
        throw new Error(data.message || 'Save failed');
      }

      setForm(emptyForm);
      setEditingId(null);
      await fetchNotes();
    } catch (e) {
      setError(e.message);
    }
  }

  function startEdit(note) {
    setEditingId(note.id);
    setForm({ title: note.title, content: note.content });
  }

  function cancelEdit() {
    setEditingId(null);
    setForm(emptyForm);
    setError('');
  }

  async function deleteNote(id) {
    setError('');
    try {
      const response = await fetch(`${API_BASE}/${id}`, { method: 'DELETE' });
      if (!response.ok) {
        const data = await response.json();
        throw new Error(data.message || 'Delete failed');
      }
      await fetchNotes();
    } catch (e) {
      setError(e.message);
    }
  }

  return (
    <main className="container">
      <h1>Mini Notes Service</h1>
      <form onSubmit={onSubmit}>
        <div className="grid">
          <div>
            <label htmlFor="title">Title</label>
            <input
              id="title"
              name="title"
              value={form.title}
              onChange={onChange}
              maxLength={255}
              required
            />
          </div>
          <div>
            <label htmlFor="content">Content</label>
            <textarea
              id="content"
              name="content"
              value={form.content}
              onChange={onChange}
              required
            />
          </div>
        </div>
        <div className="actions">
          <button className="primary" type="submit">
            {isEditing ? 'Update note' : 'Create note'}
          </button>
          {isEditing && (
            <button className="secondary" type="button" onClick={cancelEdit}>
              Cancel
            </button>
          )}
        </div>
      </form>

      {error && <p className="error">{error}</p>}
      {loading && <p>Loading...</p>}

      <section className="note-list">
        {notes.map((note) => (
          <article key={note.id} className="note">
            <strong>{note.title}</strong>
            <p>{note.content}</p>
            <div className="actions">
              <button className="secondary" type="button" onClick={() => startEdit(note)}>
                Edit
              </button>
              <button className="danger" type="button" onClick={() => deleteNote(note.id)}>
                Delete
              </button>
            </div>
          </article>
        ))}
      </section>
    </main>
  );
}
