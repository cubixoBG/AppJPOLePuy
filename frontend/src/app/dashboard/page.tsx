import { AuthProvider } from "@/context/AuthContext";
import RouteGuard from "@/components/RouteGuard";
import AdminDashboard from "@/components/AdminDashboard";

export default function DashboardPage() {
  return (
    <AuthProvider>
      <RouteGuard requiredRole="admin">
        <AdminDashboard />
      </RouteGuard>
    </AuthProvider>
  );
}
