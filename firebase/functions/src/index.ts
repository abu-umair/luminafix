import { functions, firestore } from "./firebase";

export const onUserDelete = functions.auth.user().onDelete(async ({ uid }) => {
    const doc = await firestore.collection("users").doc(uid).get();

    // User record does not exists
    if (!doc.exists) return;

    // Delete user document
    return await doc.ref.delete();
});
