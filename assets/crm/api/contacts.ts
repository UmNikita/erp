import { Contact, ContactRequest } from '../types/contact';
import api from './axios'

export async function getClientContacts(clientID: number): Promise<Contact[]>
{
    const response = await api.get(`/client/${clientID}/contacts`);
    return response.data.contacts;
}

export async function createContact(contact: ContactRequest): Promise<Contact>
{
    const response = await api.post('/contact', contact);
    return response.data;
}

export async function updateContact(contact: ContactRequest, id: number): Promise<Contact>
{
    const response = await api.patch('/contact/'+id, contact);
    return response.data;
}

export async function deleteContact(id: number): Promise<Contact>
{
    const response = await api.delete('/contact/'+id);
    return response.data;
}